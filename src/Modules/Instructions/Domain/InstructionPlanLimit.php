<?php

namespace app\Modules\Instructions\Domain;

use app\Modules\Support\Domain\SupportPlan;

final class InstructionPlanLimit
{
    private const PROJECT_STORAGE_BYTES = 1024 * 1024 * 1024;

    public function __construct(
        public readonly bool $enabled,
        public readonly int $storageBytes,
        public readonly bool $urlBindingsEnabled,
    ) {
    }

    public static function forPlan(string $plan): self
    {
        return match (SupportPlan::normalize($plan)) {
            SupportPlan::START => new self(true, self::PROJECT_STORAGE_BYTES, false),
            SupportPlan::PRO => new self(true, self::PROJECT_STORAGE_BYTES, true),
            default => new self(false, 0, false),
        };
    }
}
