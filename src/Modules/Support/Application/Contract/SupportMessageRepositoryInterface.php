<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportMessage;

interface SupportMessageRepositoryInterface
{
    public function addVisitorMessage(int $publicKey, int $conversationId, string $visitorId, string $body): SupportMessage;

    public function addOperatorMessage(int $publicKey, int $conversationId, int $operatorId, string $body): SupportMessage;

    /**
     * @return SupportMessage[]
     */
    public function listForConversation(int $publicKey, int $conversationId, ?int $afterId = null): array;
}
