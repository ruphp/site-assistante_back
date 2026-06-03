<?php

namespace app\Application\Cron\Contract;

interface ActiveClientProviderInterface
{
    public function getActiveClients(): array;
}
