<?php

namespace app\Application\Admin\Contract;

use app\Application\Admin\Dto\ClientModuleAccessView;

interface ClientAccessRepositoryInterface
{
    public function assignManagerRole(int $userId): void;

    public function syncModuleAccess(int $userId, array $modules): void;

    public function getModuleAccessView(int $userId): ClientModuleAccessView;
}
