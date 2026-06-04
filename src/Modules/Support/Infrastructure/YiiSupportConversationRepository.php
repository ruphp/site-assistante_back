<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;

final class YiiSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public function create(int $publicKey, string $visitorId): SupportConversation
    {
        $record = new SupportConversationRecord();
        $record->public_key = $publicKey;
        $record->visitor_id = $visitorId;
        $record->status = SupportConversation::STATUS_OPEN;
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

        return array_map(fn($record) => $this->map($record), $query->all());
    }

    private function map(SupportConversationRecord $record): SupportConversation
    {
        return new SupportConversation(
            id: (int)$record->id,
            publicKey: (int)$record->public_key,
            visitorId: (string)$record->visitor_id,
            status: (string)$record->status,
        );
    }
}
