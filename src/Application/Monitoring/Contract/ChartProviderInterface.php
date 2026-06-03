<?php

namespace app\Application\Monitoring\Contract;

interface ChartProviderInterface
{
    public function getChart($chartName, $chartFilters = []): array|string;

    public function getDataChart($chartName, $chartFilters = []): array|string;
}
