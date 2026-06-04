<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;

final class SupportSettingsViewData
{
    public function __construct(
        public readonly SupportSettings $settings,
        public readonly SupportPlanLimit $limit,
    ) {
    }

    public function toArray(): array
    {
        return [
            'settings' => $this->settings,
            'limit' => $this->limit,
        ];
    }
}
