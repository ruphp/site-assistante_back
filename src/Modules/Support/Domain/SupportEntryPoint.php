<?php

namespace app\Modules\Support\Domain;

final class SupportEntryPoint
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $publicKey,
        public readonly string $title,
        public readonly string $description = '',
        public readonly int $priority = 1,
        public readonly bool $enabled = true,
        public readonly int $sortOrder = 100,
    ) {
    }

    public function normalizedPriority(int $maxPriority = 5): int
    {
        return max(1, min($maxPriority, $this->priority));
    }
}
