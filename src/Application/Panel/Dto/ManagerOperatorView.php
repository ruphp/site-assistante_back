<?php

namespace app\Application\Panel\Dto;

final class ManagerOperatorView
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly string $phone,
        public readonly string $telegram,
        public readonly string $maxContact,
        public readonly bool $isOwner,
    ) {
    }
}
