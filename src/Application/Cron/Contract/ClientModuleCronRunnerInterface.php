<?php

namespace app\Application\Cron\Contract;

interface ClientModuleCronRunnerInterface
{
    public function runForClient(array $client): void;
}
