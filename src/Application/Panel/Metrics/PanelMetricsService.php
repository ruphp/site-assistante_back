<?php

namespace app\Application\Panel\Metrics;

use app\Application\Monitoring\Contract\ChartProviderInterface;
use app\Application\Panel\Metrics\Contract\PanelModuleMetricChartRepositoryInterface;

final class PanelMetricsService
{
    public function __construct(
        private readonly ChartProviderInterface $charts,
        private readonly PanelModuleMetricChartRepositoryInterface $moduleCharts,
    ) {
    }

    public function usageChart(array $filters = []): array|string
    {
        return $this->charts->getChart('usage', $filters);
    }

    public function usageData(array $filters = []): array|string
    {
        return $this->charts->getDataChart('chart_usage', $filters);
    }

    public function moduleChartsForClient(int $publicKey): array
    {
        return $this->moduleCharts->getChartsForClient($publicKey);
    }
}
