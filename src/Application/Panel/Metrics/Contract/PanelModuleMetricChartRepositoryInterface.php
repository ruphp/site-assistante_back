<?php

namespace app\Application\Panel\Metrics\Contract;

interface PanelModuleMetricChartRepositoryInterface
{
    public function getChartsForClient(int $publicKey): array;
}
