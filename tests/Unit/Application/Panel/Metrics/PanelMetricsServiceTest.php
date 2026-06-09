<?php

namespace tests\Unit\Application\Panel\Metrics;

use app\Application\Monitoring\Contract\ChartProviderInterface;
use app\Application\Panel\Metrics\Contract\PanelModuleMetricChartRepositoryInterface;
use app\Application\Panel\Metrics\PanelMetricsService;
use PHPUnit\Framework\TestCase;

final class PanelMetricsServiceTest extends TestCase
{
    public function testDelegatesUsageChartsToSharedChartCalculator(): void
    {
        $charts = new FakePanelChartService();
        $service = new PanelMetricsService($charts, new FakePanelModuleMetricChartRepository([]));

        self::assertSame(['chart' => 'usage'], $service->usageChart(['type_period' => 'day']));
        self::assertSame(['data' => 'chart_usage'], $service->usageData(['type_period' => 'day']));
        self::assertSame([
            ['chart', 'usage', ['type_period' => 'day']],
            ['data', 'chart_usage', ['type_period' => 'day']],
        ], $charts->calls);
    }

    public function testDelegatesModuleChartsToRepository(): void
    {
        $service = new PanelMetricsService(
            new FakePanelChartService(),
            new FakePanelModuleMetricChartRepository(['module-chart']),
        );

        self::assertSame(['module-chart'], $service->moduleChartsForClient(10));
    }
}

final class FakePanelChartService implements ChartProviderInterface
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

final class FakePanelModuleMetricChartRepository implements PanelModuleMetricChartRepositoryInterface
{
    public function __construct(
        private readonly array $charts,
    ) {
    }

    public function getChartsForClient(int $publicKey): array
    {
        return $this->charts;
    }
}
