<?php

namespace app\Infrastructure\Cron;

use app\Application\Cron\Contract\CronLogProcessorInterface;
use app\Application\Cron\CronLogAccumulator;
use app\Application\Cron\CronLogPeriod;
use app\Infrastructure\YiiActiveRecord\LogsApiConfiguration;
use app\Infrastructure\YiiActiveRecord\LogsApiOpen;
use app\Infrastructure\YiiActiveRecord\LogsUsageDay;
use app\Infrastructure\YiiActiveRecord\LogsUsageMonth;
use app\Infrastructure\YiiActiveRecord\LogsUsageQuart;
use app\Infrastructure\YiiActiveRecord\LogsUsageWeek;
use app\Infrastructure\YiiActiveRecord\LogsUsageYear;
use DateTime;
use Yii;

final class YiiRedisCronLogProcessor implements CronLogProcessorInterface
{
    public function __construct(
        private readonly CronLogAccumulator $logAccumulator = new CronLogAccumulator(),
        private readonly CronLogPeriod $logPeriod = new CronLogPeriod(),
    ) {
    }

    /**
     * @throws \Exception
     */
    public function processForClient(int $publicKey, int $gmt): void
    {
        $this->processConfigurationLogs($publicKey, $gmt);
        $this->processOpenLogs($publicKey, $gmt);
        $this->processUsageLogs($publicKey, $gmt);
    }

    /**
     * @throws \Exception
     */
    private function processConfigurationLogs(int $publicKey, int $gmt): void
    {
        $this->writeLog('START LOG api config ' . $publicKey, 'INFO');

        $this->processRedisLog('log/configuration/' . $publicKey, $publicKey, 'api config', function (array $mergeLog, string $mergeLogJson) use ($publicKey, $gmt): void {
            $dayLog = $this->logDate($mergeLog['date'], $gmt);
            $userId = $mergeLog['userId'] ?? 0;

            $oldLog = LogsApiConfiguration::find()->where(['public_key' => $publicKey, 'date_day' => $dayLog])->one();

            if ($oldLog === null) {
                $userJson = $this->logAccumulator->initialUserJson($userId);

                if (!LogsApiConfiguration::queryInsertLog($publicKey, $dayLog, $userJson)) {
                    $this->writeLog('RETURN OLD DATA no insert api config ' . $dayLog, 'INFO');
                    Yii::$app->redis->rpush('log/configuration/' . $publicKey, $mergeLogJson);
                }

                return;
            }

            [$usersJson, $uniqueCount] = $this->logAccumulator->mergeUsers($oldLog, $userId);

            if (!LogsApiConfiguration::queryUpdateLog($publicKey, $dayLog, $usersJson, $uniqueCount)) {
                $this->writeLog('RETURN OLD DATA no update api config ' . $dayLog, 'INFO');
                Yii::$app->redis->rpush('log/configuration/' . $publicKey, $mergeLogJson);
            }
        });
    }

    /**
     * @throws \Exception
     */
    private function processOpenLogs(int $publicKey, int $gmt): void
    {
        $this->writeLog('START LOG open ' . $publicKey, 'INFO');

        $this->processRedisLog('log/open/' . $publicKey, $publicKey, 'open', function (array $mergeLog, string $mergeLogJson) use ($publicKey, $gmt): void {
            $dayLog = $this->logDate($mergeLog['date'], $gmt);
            $userId = $mergeLog['userId'] ?? 0;
            $rolesData = $mergeLog['userRoles'] ?? [];

            $oldLog = LogsApiOpen::find()->where(['public_key' => $publicKey, 'date_day' => $dayLog])->one();

            if ($oldLog === null) {
                $userJson = $this->logAccumulator->initialUserJson($userId);
                $rolesJson = $this->logAccumulator->initialRolesJson($rolesData);

                if (!LogsApiOpen::queryInsertLog($publicKey, $dayLog, $userJson, $rolesJson)) {
                    Yii::$app->redis->rpush('log/open/' . $publicKey, $mergeLogJson);
                }

                return;
            }

            [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);

            if (!LogsApiOpen::queryUpdateLog($publicKey, $dayLog, $usersJson, $rolesJson, $uniqueCount)) {
                Yii::$app->redis->rpush('log/open/' . $publicKey, $mergeLogJson);
            }
        });
    }

    /**
     * @throws \Exception
     */
    private function processUsageLogs(int $publicKey, int $gmt): void
    {
        $this->writeLog('START LOG usage ' . $publicKey, 'INFO');

        $this->processRedisLog('log/usage/' . $publicKey, $publicKey, 'usage', function (array $mergeLog, string $mergeLogJson) use ($publicKey, $gmt): void {
            $day = new DateTime($mergeLog['date']);
            $day->modify($gmt . ' hours');

            $dayLog = $day->format('Y-m-d');
            $firstDayMonth = $day->format('Y-m-01');
            $firstDayYear = $day->format('Y-01-01');
            $mondayDay = $this->logPeriod->monday($dayLog);
            $sundayDay = $this->logPeriod->sunday($dayLog);
            $firstDayQuart = $this->logPeriod->quarterStart($day->getTimestamp());
            $lastDayQuart = $this->logPeriod->quarterEnd($day->getTimestamp());
            $userId = $mergeLog['userId'] ?? 0;
            $rolesData = $mergeLog['userRoles'] ?? [];
            $type = $mergeLog['type'];

            $this->upsertUsageDay($publicKey, $dayLog, $type, $userId, $rolesData);
            $this->upsertUsageWeek($publicKey, $mondayDay, $sundayDay, $type, $userId, $rolesData);
            $this->upsertUsageMonth($publicKey, $firstDayMonth, $type, $userId, $rolesData);
            $this->upsertUsageQuart($publicKey, $firstDayQuart, $lastDayQuart, $type, $userId, $rolesData);
            $this->upsertUsageYear($publicKey, $firstDayYear, $type, $userId, $rolesData);
        });
    }

    private function upsertUsageDay(int $publicKey, string $dayLog, string $type, $userId, array $rolesData): void
    {
        $oldLog = LogsUsageDay::find()->where(['public_key' => $publicKey, 'date_day' => $dayLog, 'type' => $type])->one();

        if ($oldLog === null) {
            LogsUsageDay::queryInsertLog(
                $publicKey,
                $dayLog,
                $this->logAccumulator->initialUserJson($userId),
                $this->logAccumulator->initialRolesJson($rolesData),
                $type
            );

            return;
        }

        [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);
        LogsUsageDay::queryUpdateLog($publicKey, $dayLog, $usersJson, $rolesJson, $uniqueCount, $type);
    }

    private function upsertUsageWeek(int $publicKey, string $mondayDay, string $sundayDay, string $type, $userId, array $rolesData): void
    {
        $oldLog = LogsUsageWeek::find()->where(['public_key' => $publicKey, 'monday_day' => $mondayDay, 'sunday_day' => $sundayDay, 'type' => $type])->one();

        if ($oldLog === null) {
            LogsUsageWeek::queryInsertLog(
                $mondayDay,
                $sundayDay,
                $publicKey,
                $type,
                $this->logAccumulator->initialUserJson($userId),
                $this->logAccumulator->initialRolesJson($rolesData)
            );

            return;
        }

        [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);
        LogsUsageWeek::queryUpdateLog($mondayDay, $sundayDay, $publicKey, $type, $usersJson, $rolesJson, $uniqueCount);
    }

    private function upsertUsageMonth(int $publicKey, string $firstDayMonth, string $type, $userId, array $rolesData): void
    {
        $oldLog = LogsUsageMonth::find()->where(['public_key' => $publicKey, 'first_day' => $firstDayMonth, 'type' => $type])->one();

        if ($oldLog === null) {
            LogsUsageMonth::queryInsertLog(
                $firstDayMonth,
                $publicKey,
                $type,
                $this->logAccumulator->initialUserJson($userId),
                $this->logAccumulator->initialRolesJson($rolesData)
            );

            return;
        }

        [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);
        LogsUsageMonth::queryUpdateLog($firstDayMonth, $publicKey, $type, $usersJson, $rolesJson, $uniqueCount);
    }

    private function upsertUsageQuart(int $publicKey, string $firstDayQuart, string $lastDayQuart, string $type, $userId, array $rolesData): void
    {
        $oldLog = LogsUsageQuart::find()->where(['public_key' => $publicKey, 'first_quart_day' => $firstDayQuart, 'last_quart_day' => $lastDayQuart, 'type' => $type])->one();

        if ($oldLog === null) {
            LogsUsageQuart::queryInsertLog(
                $firstDayQuart,
                $lastDayQuart,
                $publicKey,
                $type,
                $this->logAccumulator->initialUserJson($userId),
                $this->logAccumulator->initialRolesJson($rolesData)
            );

            return;
        }

        [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);
        LogsUsageQuart::queryUpdateLog($firstDayQuart, $lastDayQuart, $publicKey, $type, $usersJson, $rolesJson, $uniqueCount);
    }

    private function upsertUsageYear(int $publicKey, string $firstDayYear, string $type, $userId, array $rolesData): void
    {
        $oldLog = LogsUsageYear::find()->where(['public_key' => $publicKey, 'first_day' => $firstDayYear, 'type' => $type])->one();

        if ($oldLog === null) {
            LogsUsageYear::queryInsertLog(
                $firstDayYear,
                $publicKey,
                $type,
                $this->logAccumulator->initialUserJson($userId),
                $this->logAccumulator->initialRolesJson($rolesData)
            );

            return;
        }

        [$usersJson, $rolesJson, $uniqueCount] = $this->logAccumulator->mergeUsersAndRoles($oldLog, $userId, $rolesData);
        LogsUsageYear::queryUpdateLog($firstDayYear, $publicKey, $type, $usersJson, $rolesJson, $uniqueCount);
    }

    private function processRedisLog(string $key, int $publicKey, string $label, callable $handler): void
    {
        for ($i = (int)$_ENV['CRON_LIMIT_READ_REDIS_LINES']; $i > 0; --$i) {
            $mergeLogJson = Yii::$app->redis->rpop($key);

            if ($mergeLogJson === null) {
                $this->writeLog('NULL LOG ' . $label . ' $i=' . ((int)$_ENV['CRON_LIMIT_READ_REDIS_LINES'] - $i) . ' ' . $publicKey, 'INFO');
                return;
            }

            $handler(json_decode($mergeLogJson, true), $mergeLogJson);
        }
    }

    /**
     * @throws \Exception
     */
    private function logDate(string $date, int $gmt): string
    {
        $dayLog = new DateTime($date);
        $dayLog->modify($gmt . ' hours');

        return $dayLog->format('Y-m-d');
    }

    private function writeLog($message, $level = 'INFO'): void
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
