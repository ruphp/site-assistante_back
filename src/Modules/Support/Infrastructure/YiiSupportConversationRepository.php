<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;

final class YiiSupportConversationRepository implements SupportConversationRepositoryInterface
{
    public function create(int $publicKey, SupportVisitorContext $context): SupportConversation
    {
        $record = new SupportConversationRecord();
        $record->public_key = $publicKey;
        $record->visitor_id = $context->resolvedVisitorId();
        $record->visitor_email = $context->visitorEmail;
        $record->visitor_ip = $context->remoteAddr;
        $record->page_url = $context->pageUrl;
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
            visitorEmail: $record->visitor_email === null ? null : (string)$record->visitor_email,
            pageUrl: $record->page_url === null ? null : (string)$record->page_url,
            status: (string)$record->status,
        );
    }
}
