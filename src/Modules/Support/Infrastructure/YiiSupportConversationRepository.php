<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;

final class YiiSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public function create(int $publicKey, SupportVisitorContext $context, ?SupportEntryPoint $entryPoint = null): SupportConversation
    {
        $record = new SupportConversationRecord();
        $record->public_key = $publicKey;
        $record->visitor_id = $context->resolvedVisitorId();
        $record->visitor_email = $context->visitorEmail;
        $record->visitor_ip = $context->remoteAddr;
        $record->page_url = $context->pageUrl;
        $record->status = SupportConversation::STATUS_OPEN;
        $record->entry_point_id = $entryPoint?->id;
        $record->priority = $entryPoint?->priority ?? 0;
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
            visitorEmail: $record->visitor_email === null ? null : (string)$record->visitor_email,
            pageUrl: $record->page_url === null ? null : (string)$record->page_url,
            status: (string)$record->status,
            lastMessageAt: $lastMessage?->created_at === null ? null : (string)$lastMessage->created_at,
            lastSenderType: $lastMessage?->sender_type === null ? null : (string)$lastMessage->sender_type,
            entryPointId: $record->entry_point_id === null ? null : (int)$record->entry_point_id,
            priority: (int)$record->priority,
        );
    }
}
