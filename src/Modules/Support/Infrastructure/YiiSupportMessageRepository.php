<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Domain\SupportMessage;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;
use app\Infrastructure\YiiActiveRecord\Users;
use Yii;

final class YiiSupportMessageRepository implements SupportMessageRepositoryInterface
{
    public function addVisitorMessage(int $publicKey, int $conversationId, string $visitorId, string $body): SupportMessage
    {
        $record = new SupportMessageRecord();
        $record->conversation_id = $conversationId;
        $record->public_key = $publicKey;
        $record->sender_type = SupportMessage::SENDER_VISITOR;
        $record->sender_id = $visitorId;
        $record->body = $body;
        $record->save(false);
        $this->touchConversation($conversationId);

        return $this->map($record);
    }

    public function addOperatorMessage(int $publicKey, int $conversationId, int $operatorId, string $body): SupportMessage
    {
        $record = new SupportMessageRecord();
        $record->conversation_id = $conversationId;
        $record->public_key = $publicKey;
        $record->sender_type = SupportMessage::SENDER_OPERATOR;
        $record->sender_id = (string)$operatorId;
        $record->body = $body;
        $record->save(false);
        $this->touchConversation($conversationId);

        return $this->map($record);
    }

    public function listForConversation(int $publicKey, int $conversationId, ?int $afterId = null): array
    {
        $query = SupportMessageRecord::find()
            ->where([
                'public_key' => $publicKey,
                'conversation_id' => $conversationId,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->limit(100);

        if ($afterId !== null) {
            $query->andWhere(['>', 'id', $afterId]);
        }

        return array_map(fn($record) => $this->map($record), $query->all());
    }

    private function map(SupportMessageRecord $record): SupportMessage
    {
        $senderName = null;
        $senderAvatarUrl = null;
        if ((string)$record->sender_type === SupportMessage::SENDER_OPERATOR && $record->sender_id !== null) {
            $operator = Users::findOne((int)$record->sender_id);
            if ($operator instanceof Users) {
                $senderName = trim((string)$operator->name) ?: 'Менеджер';
                $avatarPath = trim((string)($operator->avatar_path ?? ''));
                $senderAvatarUrl = $avatarPath === '' ? null : rtrim(Yii::$app->request->hostInfo, '/') . '/' . ltrim($avatarPath, '/');
            }
        }

        return new SupportMessage(
            id: (int)$record->id,
            conversationId: (int)$record->conversation_id,
            publicKey: (int)$record->public_key,
            senderType: (string)$record->sender_type,
            senderId: $record->sender_id === null ? null : (string)$record->sender_id,
            body: (string)$record->body,
            createdAt: $record->created_at === null ? null : (string)$record->created_at,
            senderName: $senderName,
            senderAvatarUrl: $senderAvatarUrl,
        );
    }

    private function touchConversation(int $conversationId): void
    {
        SupportConversationRecord::updateAll(
            ['updated_at' => new \yii\db\Expression('NOW()')],
            ['id' => $conversationId],
        );
    }
}
