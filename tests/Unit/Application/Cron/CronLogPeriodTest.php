<?php

namespace tests\Unit\Application\Cron;

use app\Application\Cron\CronLogPeriod;
use PHPUnit\Framework\TestCase;

final class CronLogPeriodTest extends TestCase
{
    public function testReturnsWeekBounds(): void
    {
        $period = new CronLogPeriod();

        self::assertSame('2026-06-01', $period->monday('2026-06-03'));
        self::assertSame('2026-06-07', $period->sunday('2026-06-03'));
    }

    public function testReturnsQuarterBounds(): void
    {
        $period = new CronLogPeriod();
        $timestamp = strtotime('2026-06-03');

        self::assertSame('2026-04-01', $period->quarterStart($timestamp));
        self::assertSame('2026-06-30', $period->quarterEnd($timestamp));
    }
}
