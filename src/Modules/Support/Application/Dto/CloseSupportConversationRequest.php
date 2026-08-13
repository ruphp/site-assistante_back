<?php

namespace app\Modules\Support\Application\Dto;

final class CloseSupportConversationRequest
{
    public function __construct(
        public readonly int $publicKey,
        public readonly int $conversationId,
        public readonly SupportVisitorContext $context,
    ) {
    }
}
