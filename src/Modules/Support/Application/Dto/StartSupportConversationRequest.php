<?php

namespace app\Modules\Support\Application\Dto;

final class StartSupportConversationRequest
{
    public function __construct(
        public readonly int $publicKey,
        public readonly SupportVisitorContext $context,
        public readonly ?string $firstMessage = null,
        public readonly ?int $entryPointId = null,
    ) {
    }
}
