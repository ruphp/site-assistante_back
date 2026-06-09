<?php

namespace app\Modules\Support\Application\Dto;

final class SendSupportMessageRequest
{
    public function __construct(
        public readonly int $publicKey,
        public readonly int $conversationId,
        public readonly SupportVisitorContext $context,
        public readonly string $body,
    ) {
    }
}
