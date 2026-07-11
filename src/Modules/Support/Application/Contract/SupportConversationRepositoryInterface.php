<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Domain\SupportEntryPoint;

interface SupportConversationRepositoryInterface
{
    public function create(int $publicKey, SupportVisitorContext $context, ?SupportEntryPoint $entryPoint = null): SupportConversation;

    public function getOpenForVisitor(int $publicKey, int $conversationId, string $visitorId): ?SupportConversation;

    public function findOpenByEmail(int $publicKey, string $visitorEmail): ?SupportConversation;

    public function getForClient(int $publicKey, int $conversationId): ?SupportConversation;

    public function markVisitorActivity(int $publicKey, int $conversationId): bool;

    public function markOperatorReply(int $publicKey, int $conversationId): bool;

    public function markOperatorSeen(int $publicKey, int $conversationId): bool;

    public function closeExpiredAfterOperatorSeen(int $timeoutSeconds): int;

    public function closeExpiredAfterOperatorReply(int $timeoutSeconds): int;

    public function closeForClient(int $publicKey, int $conversationId): bool;

    public function deleteForClient(int $publicKey, int $conversationId): bool;

    /**
     * @return SupportConversation[]
     */
    public function listForClient(int $publicKey, ?string $status = null, int $limit = 50): array;
}
