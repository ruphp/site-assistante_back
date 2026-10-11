<?php

namespace tests\Unit\Module\Support\Domain;

use app\Modules\Onboarding\Domain\OnboardingPlanLimit;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Surveys\Domain\SurveyPlanLimit;
use PHPUnit\Framework\TestCase;

final class ModulePlanFeatureTest extends TestCase
{
    public function testRoleFilteringIsAvailableOnlyOnPro(): void
    {
        self::assertFalse(OnboardingPlanLimit::forPlan(SupportPlan::START)->rolesEnabled);
        self::assertTrue(OnboardingPlanLimit::forPlan(SupportPlan::PRO)->rolesEnabled);
        self::assertFalse(SurveyPlanLimit::forPlan(SupportPlan::START)->rolesEnabled);
        self::assertTrue(SurveyPlanLimit::forPlan(SupportPlan::PRO)->rolesEnabled);
    }
}
