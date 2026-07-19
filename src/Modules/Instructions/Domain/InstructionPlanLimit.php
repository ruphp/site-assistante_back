<?php

namespace app\Modules\Instructions\Domain;

use app\Modules\Support\Domain\SupportPlan;

final class InstructionPlanLimit
{
    public function __construct(
        public readonly bool $enabled,
        public readonly int $storageBytes,
        public readonly bool $urlBindingsEnabled,
    ) {
    }

    public static function forPlan(string $plan): self
    {
        return match (SupportPlan::normalize($plan)) {
            SupportPlan::START => new self(true, 5 * 1024 * 1024, false),
            SupportPlan::PRO => new self(true, 50 * 1024 * 1024, true),
            default => new self(false, 0, false),
        };
    }
}
