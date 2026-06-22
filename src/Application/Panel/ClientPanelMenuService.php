<?php

namespace app\Application\Panel;

use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;

final class ClientPanelMenuService
{
    public function __construct(
        private readonly ClientModuleMenuRepositoryInterface $moduleMenus,
    ) {
    }

    public function baseMenu(): array
    {
        return [
            'manager/params' => 'Параметры',
            'manager/designe' => 'Оформление',
            'manager/roles' => 'Роли',
        ];
    }

    public function moduleMenusForClient(int $publicKey): array
    {
        return $this->moduleMenus->getModuleMenusForClient($publicKey);
    }
}
