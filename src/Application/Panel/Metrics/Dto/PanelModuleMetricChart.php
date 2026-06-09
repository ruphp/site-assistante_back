<?php

namespace app\Application\Panel\Metrics\Dto;

final class PanelModuleMetricChart
{
    public function __construct(
        public readonly string $name,
        public readonly string $htmlView,
        public readonly string $jsView,
        public readonly array $data,
    ) {
    }
}
