<?php

namespace app\Application\Panel\Dto;

use app\Domain\Client\ClientModuleAccess;

final class ParamsViewData
{
    public function __construct(
        public readonly mixed $params,
        public readonly string $code,
        public readonly ClientModuleAccess $moduleAccess,
    ) {
    }

    public function toArray(): array
    {
        return [
            'params' => $this->params,
            'code' => $this->code,
            'moduleAccess' => $this->moduleAccess,
        ];
    }
}
