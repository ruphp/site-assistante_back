<?php

namespace app\Application\Panel;

use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlan;

final class ClientPanelMenuService
{
    public function __construct(
        private readonly ClientModuleMenuRepositoryInterface $moduleMenus,
        private readonly SupportSettingsRepositoryInterface $supportSettings,
    ) {
    }

    public function baseMenu(int $publicKey, bool $isOwner = true): array
    {
        if (!$isOwner) {
            return [
                'manager/support/conversations' => 'Диалоги',
                'manager/instructions' => 'Инструкции',
            ];
        }

        $menu = [
            'manager/params' => 'Параметры',
            'manager/designe' => 'Оформление',
            'manager/limits' => 'Лимиты',
            'manager/instructions' => 'Инструкции',
        ];

        if ($this->rolesEnabledForClient($publicKey)) {
            $menu['manager/roles'] = 'Роли';
        }

        if ($this->operatorsEnabledForClient($publicKey)) {
            $menu['manager/operators'] = 'Менеджеры';
        }

        return $menu;
    }

    public function rolesEnabledForClient(int $publicKey): bool
    {
        return SupportPlan::normalize($this->supportSettings->getForClient($publicKey)->plan) === SupportPlan::PRO;
    }

    public function operatorsEnabledForClient(int $publicKey): bool
    {
        return SupportPlan::normalize($this->supportSettings->getForClient($publicKey)->plan) !== SupportPlan::FREE;
    }

    public function moduleMenusForClient(int $publicKey): array
    {
        return $this->moduleMenus->getModuleMenusForClient($publicKey);
    }
}
