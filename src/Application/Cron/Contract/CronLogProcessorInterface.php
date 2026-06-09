<?php

namespace app\Application\Cron\Contract;

interface CronLogProcessorInterface
{
    public function processForClient(int $publicKey, int $gmt): void;
}
