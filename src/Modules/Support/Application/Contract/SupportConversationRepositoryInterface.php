<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;

interface SupportConversationRepositoryInterface
{
    public function create(int $publicKey, string $visitorId): SupportConversation;

    public function getOpenForVisitor(int $publicKey, int $conversationId, string $visitorId): ?SupportConversation;

    public function getForClient(int $publicKey, int $conversationId): ?SupportConversation;

    /**
     * @return SupportConversation[]
     */
    public function listForClient(int $publicKey, ?string $status = null, int $limit = 50): array;
}
