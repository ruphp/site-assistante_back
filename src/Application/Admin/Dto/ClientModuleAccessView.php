<?php

namespace app\Application\Admin\Dto;

final class ClientModuleAccessView
{
    public function __construct(
        private readonly array $items,
    ) {
    }

    public function selectedModules(): array
    {
        $modules = [];

        foreach ($this->items as $item) {
            $modules[$item->key] = $item->enabled;
        }

        return $modules;
    }

    public function items(): array
    {
        return $this->items;
    }

    public function moduleLabels(): array
    {
        $labels = [];

        foreach ($this->items as $item) {
            $labels[$item->key] = $item->label;
        }

        return $labels;
    }

}
