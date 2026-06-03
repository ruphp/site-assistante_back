<?php

namespace tests\Unit\Application\Cron;

use app\Application\Cron\CronLogAccumulator;
use PHPUnit\Framework\TestCase;

final class CronLogAccumulatorTest extends TestCase
{
    public function testCreatesInitialUserJson(): void
    {
        $accumulator = new CronLogAccumulator();

        self::assertSame('{"0":42}', $accumulator->initialUserJson(42));
    }

    public function testCreatesInitialRolesJson(): void
    {
        $accumulator = new CronLogAccumulator();

        self::assertSame('{"8":1,"20":1}', $accumulator->initialRolesJson([8, 20]));
    }

    public function testMergesUsersAndRoles(): void
    {
        $oldLog = (object)[
            'json_users' => [0 => 42, 1 => 42],
            'json_roles_data' => [8 => 2],
        ];

        $accumulator = new CronLogAccumulator();

        [$usersJson, $rolesJson, $uniqueCount] = $accumulator->mergeUsersAndRoles($oldLog, 77, [8, 20]);

        self::assertSame('{"0":42,"2":77}', $usersJson);
        self::assertSame('{"8":3,"20":1}', $rolesJson);
        self::assertSame(2, $uniqueCount);
    }
}
