<?php

namespace app\Application\Client\Contract;

use app\Domain\Client\ClientModuleAccess;

interface ClientModuleAccessRepositoryInterface
{
    public function getForClient(int $publicKey): ClientModuleAccess;
}
