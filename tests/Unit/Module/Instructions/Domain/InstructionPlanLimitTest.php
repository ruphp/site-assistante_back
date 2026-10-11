<?php

namespace tests\Unit\Module\Instructions\Domain;

use app\Modules\Instructions\Domain\InstructionPlanLimit;
use app\Modules\Support\Domain\SupportPlan;
use PHPUnit\Framework\TestCase;

final class InstructionPlanLimitTest extends TestCase
{
    public function testFreeProvidesBasicInstructionsWithoutAdvancedFeatures(): void
    {
        $limit = InstructionPlanLimit::forPlan(SupportPlan::FREE);

        self::assertTrue($limit->enabled);
        self::assertSame(5, $limit->maxCategories);
        self::assertSame(10, $limit->maxArticlesPerCategory);
        self::assertFalse($limit->nestedCategoriesEnabled);
        self::assertFalse($limit->searchEnabled);
        self::assertFalse($limit->rolesEnabled);
    }

    public function testStartEnablesSearchAndNestedCategories(): void
    {
        $limit = InstructionPlanLimit::forPlan(SupportPlan::START);

        self::assertTrue($limit->enabled);
        self::assertTrue($limit->searchEnabled);
        self::assertTrue($limit->nestedCategoriesEnabled);
        self::assertFalse($limit->rolesEnabled);
        self::assertNull($limit->maxCategories);
        self::assertNull($limit->maxArticlesPerCategory);
    }

    public function testProEnablesRoleFiltering(): void
    {
        $limit = InstructionPlanLimit::forPlan(SupportPlan::PRO);

        self::assertTrue($limit->rolesEnabled);
        self::assertTrue($limit->urlBindingsEnabled);
    }
}
