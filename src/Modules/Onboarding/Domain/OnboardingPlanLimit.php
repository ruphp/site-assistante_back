<?php

namespace app\Modules\Onboarding\Domain;

use app\Modules\Support\Domain\SupportPlan;

final class OnboardingPlanLimit
{
    public function __construct(
        public readonly bool $enabled,
        public readonly bool $urlBindingsEnabled,
    ) {
    }

    public static function forPlan(string $plan): self
    {
        return match (SupportPlan::normalize($plan)) {
            SupportPlan::START => new self(true, false),
            SupportPlan::PRO => new self(true, true),
            default => new self(false, false),
        };
    }
}
