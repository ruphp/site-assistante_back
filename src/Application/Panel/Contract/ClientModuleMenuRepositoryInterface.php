<?php

namespace app\Application\Panel\Contract;

interface ClientModuleMenuRepositoryInterface
{
    public function getModuleMenusForClient(int $publicKey): array;
}
