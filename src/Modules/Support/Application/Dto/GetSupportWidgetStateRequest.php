<?php

namespace app\Modules\Support\Application\Dto;

final class GetSupportWidgetStateRequest
{
    public function __construct(
        public readonly int $publicKey,
        public readonly SupportVisitorContext $context,
    ) {
    }
}
