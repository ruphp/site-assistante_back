<?php

namespace app\Infrastructure\Admin;

use app\Application\Admin\Contract\ClientAccessRepositoryInterface;
use app\Application\Admin\Dto\ClientModuleAccessView;
use app\Application\Admin\Dto\ClientModuleAccessViewItem;
use Exception;
use Yii;

final class YiiClientAccessRepository implements ClientAccessRepositoryInterface
{
    public function assignManagerRole(int $userId): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('manager');

        if ($role !== null) {
            $auth->assign($role, $userId);
        }
    }

    public function syncModuleAccess(int $userId, array $modules): void
    {
        $auth = Yii::$app->authManager;
        $allowedModules = array_fill_keys(array_keys($auth->getPermissionsByRole('accesses_modules')), true);

        foreach ($modules as $module => $isAccess) {
            if (!isset($allowedModules[(string)$module])) {
                continue;
            }

            $permission = $auth->getPermission((string)$module);

            if ($permission === null) {
                continue;
            }

            if ((int)$isAccess) {
                try {
                    $auth->assign($permission, $userId);
                } catch (Exception) {
                    // Permission is already assigned.
                }
            } else {
                $auth->revoke($permission, $userId);
            }
        }
    }

    public function getModuleAccessView(int $userId): ClientModuleAccessView
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByRole('accesses_modules');
        $orderModules = json_decode($_ENV['ORDER_MODULES'] ?? '[]', true) ?: [];

        uksort($permissions, static function ($key1, $key2) use ($orderModules) {
            $pos1 = array_search($key1, $orderModules, true);
            $pos2 = array_search($key2, $orderModules, true);

            return ($pos1 === false ? PHP_INT_MAX : $pos1) <=> ($pos2 === false ? PHP_INT_MAX : $pos2);
        });

        $userPermissions = $auth->getPermissionsByUser($userId);
        $items = [];

        foreach ($permissions as $module) {
            $items[] = new ClientModuleAccessViewItem(
                $module->name,
                $module->description,
                isset($userPermissions[$module->name]),
            );
        }

        return new ClientModuleAccessView($items);
    }
}
