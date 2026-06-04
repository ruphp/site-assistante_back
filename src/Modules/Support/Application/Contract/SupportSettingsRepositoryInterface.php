<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportSettings;

interface SupportSettingsRepositoryInterface
{
    public function getForClient(int $publicKey): SupportSettings;

    public function save(SupportSettings $settings): bool;
}
