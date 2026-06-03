<?php

namespace app\Application\Admin\Dto;

final class ClientModuleAccessViewItem
{
    public function __construct(
        public readonly string $key,
        public readonly string $label,
        public readonly bool $enabled,
    ) {
    }
}
