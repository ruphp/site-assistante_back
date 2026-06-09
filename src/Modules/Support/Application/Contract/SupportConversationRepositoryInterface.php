<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Domain\SupportEntryPoint;

interface SupportConversationRepositoryInterface
{
    public function create(int $publicKey, SupportVisitorContext $context, ?SupportEntryPoint $entryPoint = null): SupportConversation;

    public function getOpenForVisitor(int $publicKey, int $conversationId, string $visitorId): ?SupportConversation;

    public function getForClient(int $publicKey, int $conversationId): ?SupportConversation;

    /**
     * @return SupportConversation[]
     */
    public function listForClient(int $publicKey, ?string $status = null, int $limit = 50): array;
}
