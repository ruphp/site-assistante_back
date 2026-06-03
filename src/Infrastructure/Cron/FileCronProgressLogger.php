<?php

namespace app\Infrastructure\Cron;

use app\Application\Cron\Contract\CronProgressLoggerInterface;

final class FileCronProgressLogger implements CronProgressLoggerInterface
{
    public function write(string $message, string $level = 'INFO'): void
    {
        $date = date('Y-m-d H:i:s');
        $pid = getmypid();
        $logLine = sprintf(
            "[%s] [%s] [PID:%d] %s\n",
            $date,
            str_pad($level, 7),
            $pid,
            $message
        );

        file_put_contents('/var/log/cron.log', $logLine, FILE_APPEND);
    }
}
