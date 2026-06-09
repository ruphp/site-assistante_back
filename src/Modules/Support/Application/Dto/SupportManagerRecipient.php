<?php

namespace app\Modules\Support\Application\Dto;

final class SupportManagerRecipient
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $email,
    ) {
    }
}
