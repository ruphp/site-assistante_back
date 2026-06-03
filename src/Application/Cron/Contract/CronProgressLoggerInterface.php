<?php

namespace app\Application\Cron\Contract;

interface CronProgressLoggerInterface
{
    public function write(string $message, string $level = 'INFO'): void;
}
