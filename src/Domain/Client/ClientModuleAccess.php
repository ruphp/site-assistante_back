<?php

namespace app\Domain\Client;

final class ClientModuleAccess
{
    public function __construct(
        private readonly array $modules,
    ) {
    }

    public function filterAllowed(array $requestedModules): array
    {
        return array_values(array_intersect($requestedModules, $this->modules));
    }

    public function allows(string $module): bool
    {
        return in_array($module, $this->modules, true);
    }
}
