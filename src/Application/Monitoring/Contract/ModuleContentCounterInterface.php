<?php

namespace app\Application\Monitoring\Contract;

interface ModuleContentCounterInterface
{
    public function count(string $moduleKey, int $publicKey): int;
}
