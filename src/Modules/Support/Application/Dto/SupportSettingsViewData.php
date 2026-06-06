<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;

final class SupportSettingsViewData
{
    public function __construct(
        public readonly SupportSettings $settings,
        public readonly SupportPlanLimit $limit,
        public readonly array $managerRecipients = [],
        public readonly string $defaultNotificationEmail = '',
    ) {
    }

    public function toArray(): array
    {
        return [
            'settings' => $this->settings,
            'limit' => $this->limit,
            'managerRecipients' => $this->managerRecipients,
            'defaultNotificationEmail' => $this->defaultNotificationEmail,
        ];
    }
}
