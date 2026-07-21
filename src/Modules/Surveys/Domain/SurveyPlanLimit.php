<?php

namespace app\Modules\Surveys\Domain;

use app\Modules\Support\Domain\SupportPlan;

final class SurveyPlanLimit
{
    public function __construct(
        public readonly bool $enabled,
        public readonly bool $urlBindingsEnabled,
    ) {
    }

    public static function forPlan(string $plan): self
    {
        $plan = SupportPlan::normalize($plan);

        return match ($plan) {
            SupportPlan::START => new self(true, false),
            SupportPlan::PRO => new self(true, true),
            default => new self(false, false),
        };
    }
}
