<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportEntryPointRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;

final class YiiSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public function create(int $publicKey, SupportVisitorContext $context, ?SupportEntryPoint $entryPoint = null): SupportConversation
    {
        $record = new SupportConversationRecord();
        $record->public_key = $publicKey;
        $record->visitor_id = $context->resolvedVisitorId();
        $record->visitor_name = $context->visitorName;
        $record->visitor_email = $context->visitorEmail;
        $record->visitor_ip = $context->remoteAddr;
        $record->page_url = $context->pageUrl;
        $record->status = SupportConversation::STATUS_OPEN;
        $record->entry_point_id = $entryPoint?->id;
        $record->priority = $entryPoint?->priority ?? 0;
        $record->last_visitor_activity_at = new \yii\db\Expression('NOW()');
        $record->save(false);

        return $this->map($record);
    }

    public function getOpenForVisitor(int $publicKey, int $conversationId, string $visitorId): ?SupportConversation
    {
        $record = SupportConversationRecord::find()
            ->where([
                'id' => $conversationId,
                'public_key' => $publicKey,
                'visitor_id' => $visitorId,
                'status' => SupportConversation::STATUS_OPEN,
            ])
            ->one();

        return $record ? $this->map($record) : null;
    }

    public function getForClient(int $publicKey, int $conversationId): ?SupportConversation
    {
        $record = SupportConversationRecord::find()
            ->where([
                'id' => $conversationId,
                'public_key' => $publicKey,
            ])
            ->one();

        return $record ? $this->map($record) : null;
    }

    public function markVisitorActivity(int $publicKey, int $conversationId): bool
    {
        return SupportConversationRecord::updateAll([
            'last_visitor_activity_at' => new \yii\db\Expression('NOW()'),
            'operator_seen_at' => null,
            'updated_at' => new \yii\db\Expression('NOW()'),
        ], [
            'id' => $conversationId,
            'public_key' => $publicKey,
        ]) > 0;
    }

    public function markOperatorReply(int $publicKey, int $conversationId): bool
    {
        return SupportConversationRecord::updateAll([
            'operator_replied_at' => new \yii\db\Expression('NOW()'),
            'operator_seen_at' => null,
            'updated_at' => new \yii\db\Expression('NOW()'),
        ], [
            'id' => $conversationId,
            'public_key' => $publicKey,
        ]) > 0;
    }

    public function markOperatorSeen(int $publicKey, int $conversationId): bool
    {
        return SupportConversationRecord::updateAll([
            'operator_seen_at' => new \yii\db\Expression('NOW()'),
            'updated_at' => new \yii\db\Expression('NOW()'),
        ], [
            'id' => $conversationId,
            'public_key' => $publicKey,
            'status' => SupportConversation::STATUS_OPEN,
        ]) > 0;
    }

    public function closeExpiredAfterOperatorSeen(int $timeoutSeconds): int
    {
        $threshold = (new \DateTimeImmutable(sprintf('-%d seconds', max(0, $timeoutSeconds))))->format('Y-m-d H:i:s');

        return SupportConversationRecord::updateAll([
            'status' => SupportConversation::STATUS_CLOSED,
            'closed_at' => new \yii\db\Expression('NOW()'),
            'updated_at' => new \yii\db\Expression('NOW()'),
        ], [
            'and',
            ['status' => SupportConversation::STATUS_OPEN],
            ['not', ['operator_seen_at' => null]],
            ['<=', 'operator_seen_at', $threshold],
        ]);
    }

    public function findOpenByEmail(int $publicKey, string $visitorEmail): ?SupportConversation
    {
        $email = mb_strtolower(trim($visitorEmail));
        if ($email === '') {
            return null;
        }

        $record = SupportConversationRecord::find()
            ->where([
                'public_key' => $publicKey,
                'status' => SupportConversation::STATUS_OPEN,
            ])
            ->andWhere(new \yii\db\Expression('LOWER(visitor_email) = :email', [':email' => $email]))
            ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
            ->one();

        return $record ? $this->map($record) : null;
    }

    public function closeForClient(int $publicKey, int $conversationId): bool
    {
        return SupportConversationRecord::updateAll([
            'status' => SupportConversation::STATUS_CLOSED,
            'closed_at' => new \yii\db\Expression('NOW()'),
            'updated_at' => new \yii\db\Expression('NOW()'),
        ], [
            'id' => $conversationId,
            'public_key' => $publicKey,
        ]) > 0;
    }

    public function deleteForClient(int $publicKey, int $conversationId): bool
    {
        return SupportConversationRecord::deleteAll([
            'id' => $conversationId,
            'public_key' => $publicKey,
        ]) > 0;
    }

    public function listForClient(int $publicKey, ?string $status = null, int $limit = 50): array
    {
        $query = SupportConversationRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['updated_at' => SORT_DESC, 'id' => SORT_DESC])
            ->limit($limit);

        if ($status !== null) {
            $query->andWhere(['status' => $status]);
        }

        $conversations = array_map(fn($record) => $this->map($record), $query->all());

        usort($conversations, static function (SupportConversation $left, SupportConversation $right): int {
            if ($left->waitsForOperator() !== $right->waitsForOperator()) {
                return $left->waitsForOperator() ? -1 : 1;
            }

            if ($left->priority !== $right->priority) {
                return $right->priority <=> $left->priority;
            }

            if ($left->waitsForOperator()) {
                return $right->waitingSeconds() <=> $left->waitingSeconds();
            }

            return strcmp((string)$right->lastMessageAt, (string)$left->lastMessageAt);
        });

        return $conversations;
    }

    private function map(SupportConversationRecord $record): SupportConversation
    {
        $lastMessage = SupportMessageRecord::find()
            ->where([
                'public_key' => (int)$record->public_key,
                'conversation_id' => (int)$record->id,
            ])
            ->orderBy(['id' => SORT_DESC])
            ->one();

        return new SupportConversation(
            id: (int)$record->id,
            publicKey: (int)$record->public_key,
            visitorId: (string)$record->visitor_id,
            visitorName: $record->visitor_name === null ? null : (string)$record->visitor_name,
            visitorEmail: $record->visitor_email === null ? null : (string)$record->visitor_email,
            pageUrl: $record->page_url === null ? null : (string)$record->page_url,
            status: (string)$record->status,
            createdAt: $record->created_at === null ? null : (string)$record->created_at,
            lastMessageAt: $lastMessage?->created_at === null ? null : (string)$lastMessage->created_at,
            lastSenderType: $lastMessage?->sender_type === null ? null : (string)$lastMessage->sender_type,
            operatorRepliedAt: $record->operator_replied_at === null ? null : (string)$record->operator_replied_at,
            operatorSeenAt: $record->operator_seen_at === null ? null : (string)$record->operator_seen_at,
            lastVisitorActivityAt: $record->last_visitor_activity_at === null ? null : (string)$record->last_visitor_activity_at,
            entryPointId: $record->entry_point_id === null ? null : (int)$record->entry_point_id,
            entryPointTitle: $this->entryPointTitle((int)$record->public_key, $record->entry_point_id),
            priority: (int)$record->priority,
        );
    }

    private function entryPointTitle(int $publicKey, mixed $entryPointId): ?string
    {
        if ($entryPointId === null) {
            return null;
        }

        $entryPoint = SupportEntryPointRecord::findOne([
            'id' => (int)$entryPointId,
            'public_key' => $publicKey,
        ]);

        return $entryPoint?->title === null ? null : (string)$entryPoint->title;
    }
}
