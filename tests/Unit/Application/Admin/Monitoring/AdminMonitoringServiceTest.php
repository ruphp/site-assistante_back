<?php

namespace tests\Unit\Application\Admin\Monitoring;

use app\Application\Admin\Monitoring\AdminMonitoringService;
use app\Application\Monitoring\Contract\ChartProviderInterface;
use PHPUnit\Framework\TestCase;

final class AdminMonitoringServiceTest extends TestCase
{
    public function testDelegatesChartRequestsToSharedChartCalculator(): void
    {
        $charts = new FakeAdminChartService();
        $service = new AdminMonitoringService($charts);

        self::assertSame(['chart' => 'usage'], $service->chart('usage', ['period' => 'day']));
        self::assertSame(['data' => 'chart_usage'], $service->dataChart('chart_usage', ['period' => 'day']));
        self::assertSame([
            ['chart', 'usage', ['period' => 'day']],
            ['data', 'chart_usage', ['period' => 'day']],
        ], $charts->calls);
    }
}

final class FakeAdminChartService implements ChartProviderInterface
{
    public array $calls = [];

    public function getChart($chart_name, $chart_filters = []): array|string
    {
        $this->calls[] = ['chart', $chart_name, $chart_filters];

        return ['chart' => $chart_name];
    }

    public function getDataChart($chart_name, $chart_filters = []): array|string
    {
        $this->calls[] = ['data', $chart_name, $chart_filters];

        return ['data' => $chart_name];
    }
}
