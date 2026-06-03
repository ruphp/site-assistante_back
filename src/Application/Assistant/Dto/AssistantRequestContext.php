<?php

namespace app\Application\Assistant\Dto;

final class AssistantRequestContext
{
    public function __construct(
        public readonly string $pathname = '',
        public readonly string $getparams = '',
        public readonly mixed $userId = null,
        public readonly array $userRoles = [],
        public readonly ?string $stringRoles = null,
        public readonly string $remoteAddr = '0.0.0.0',
        public readonly string $originHost = '',
    ) {
    }
}
