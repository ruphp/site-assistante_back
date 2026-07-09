<?php

namespace app\Application\Panel\Dto;

final class ClientProjectView
{
    public function __construct(
        public readonly int $id,
        public readonly int $ownerPublicKey,
        public readonly int $publicKey,
        public readonly string $name,
        public readonly string $domain = '',
        public readonly bool $enabled = true,
        public readonly bool $isDefault = false,
    ) {
    }
}
