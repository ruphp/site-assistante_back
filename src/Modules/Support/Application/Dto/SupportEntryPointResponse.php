<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportEntryPoint;

final class SupportEntryPointResponse
{
    public function __construct(
        private readonly SupportEntryPoint $entryPoint,
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->entryPoint->id,
            'title' => $this->entryPoint->title,
            'description' => $this->entryPoint->description,
            'priority' => $this->entryPoint->priority,
            'enabled' => $this->entryPoint->enabled,
            'sort_order' => $this->entryPoint->sortOrder,
        ];
    }
}
