<?php

namespace tests\Integration\Infrastructure\YiiActiveRecord;

use app\Infrastructure\YiiActiveRecord\LogsApiConfiguration;
use app\Infrastructure\YiiActiveRecord\LogsApiOpen;
use app\Infrastructure\YiiActiveRecord\LogsUsageDay;
use app\Infrastructure\YiiActiveRecord\LogsUsageMonth;
use app\Infrastructure\YiiActiveRecord\LogsUsageQuart;
use app\Infrastructure\YiiActiveRecord\LogsUsageWeek;
use app\Infrastructure\YiiActiveRecord\LogsUsageYear;
use tests\Integration\Support\YiiIntegrationTestCase;

final class LogJsonbPersistenceTest extends YiiIntegrationTestCase
{
    public function testConfigurationLogStoresJsonb(): void
    {
        $publicKey = 990001;
        $this->createClient($publicKey);

        LogsApiConfiguration::queryInsertLog($publicKey, '2099-01-01', '{"0":42}');
        LogsApiConfiguration::queryUpdateLog($publicKey, '2099-01-01', '{"0":42,"1":77}', 2);

        self::assertSame(
            ['0' => 42, '1' => 77],
            $this->jsonColumn('logs_api_configuration', 'json_users', [
                'public_key' => $publicKey,
                'date_day' => '2099-01-01',
            ])
        );
    }

    public function testOpenLogStoresUsersAndRolesJsonb(): void
    {
        $publicKey = 990002;
        $this->createClient($publicKey);

        LogsApiOpen::queryInsertLog($publicKey, '2099-01-01', '{"0":42}', '{"8":1}');
        LogsApiOpen::queryUpdateLog($publicKey, '2099-01-01', '{"0":42,"1":77}', '{"8":2,"20":1}', 2);

        $where = [
            'public_key' => $publicKey,
            'date_day' => '2099-01-01',
        ];

        self::assertSame(['0' => 42, '1' => 77], $this->jsonColumn('logs_api_open', 'json_users', $where));
        self::assertSame(['8' => 2, '20' => 1], $this->jsonColumn('logs_api_open', 'json_roles_data', $where));
    }

    public function testUsagePeriodLogsStoreJsonb(): void
    {
        $publicKey = 990003;
        $this->createClient($publicKey);

        LogsUsageDay::queryInsertLog($publicKey, '2099-01-01', '{"0":42}', '{"8":1}', 'courses');
        LogsUsageDay::queryUpdateLog($publicKey, '2099-01-01', '{"0":42,"1":77}', '{"8":2}', 2, 'courses');

        LogsUsageWeek::queryInsertLog('2099-01-05', '2099-01-11', $publicKey, 'courses', '{"0":42}', '{"8":1}');
        LogsUsageWeek::queryUpdateLog('2099-01-05', '2099-01-11', $publicKey, 'courses', '{"0":42,"1":77}', '{"8":2}', 2);

        LogsUsageMonth::queryInsertLog('2099-01-01', $publicKey, 'courses', '{"0":42}', '{"8":1}');
        LogsUsageMonth::queryUpdateLog('2099-01-01', $publicKey, 'courses', '{"0":42,"1":77}', '{"8":2}', 2);

        LogsUsageQuart::queryInsertLog('2099-01-01', '2099-03-31', $publicKey, 'courses', '{"0":42}', '{"8":1}');
        LogsUsageQuart::queryUpdateLog('2099-01-01', '2099-03-31', $publicKey, 'courses', '{"0":42,"1":77}', '{"8":2}', 2);

        LogsUsageYear::queryInsertLog('2099-01-01', $publicKey, 'courses', '{"0":42}', '{"8":1}');
        LogsUsageYear::queryUpdateLog('2099-01-01', $publicKey, 'courses', '{"0":42,"1":77}', '{"8":2}', 2);

        self::assertSame(['0' => 42, '1' => 77], $this->jsonColumn('day_usage_logs', 'json_users', [
            'public_key' => $publicKey,
            'date_day' => '2099-01-01',
            'type' => 'courses',
        ]));

        self::assertSame(['8' => 2], $this->jsonColumn('year_usage_logs', 'json_roles_data', [
            'public_key' => $publicKey,
            'first_day' => '2099-01-01',
            'type' => 'courses',
        ]));
    }
}
