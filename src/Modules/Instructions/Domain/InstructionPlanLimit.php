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
        public readonly bool $searchEnabled,
        public readonly bool $nestedCategoriesEnabled,
        public readonly bool $rolesEnabled,
        public readonly ?int $maxCategories,
        public readonly ?int $maxArticlesPerCategory,
    ) {
    }

    public static function forPlan(string $plan): self
    {
        return match (SupportPlan::normalize($plan)) {
            SupportPlan::FREE => new self(true, self::PROJECT_STORAGE_BYTES, false, false, false, false, 5, 10),
            SupportPlan::START => new self(true, self::PROJECT_STORAGE_BYTES, false, true, true, false, null, null),
            SupportPlan::PRO => new self(true, self::PROJECT_STORAGE_BYTES, true, true, true, true, null, null),
        };
    }
}
