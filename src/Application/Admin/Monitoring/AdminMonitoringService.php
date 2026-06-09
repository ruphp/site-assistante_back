<?php

namespace app\Application\Admin\Monitoring;

use app\Application\Monitoring\Contract\ChartProviderInterface;

final class AdminMonitoringService
{
    public function __construct(
        private readonly ChartProviderInterface $charts,
    ) {
    }

    public function chart(string $name, array $filters = []): array|string
    {
        return $this->charts->getChart($name, $filters);
    }

    public function dataChart(string $name, array $filters = []): array|string
    {
        return $this->charts->getDataChart($name, $filters);
    }
}
