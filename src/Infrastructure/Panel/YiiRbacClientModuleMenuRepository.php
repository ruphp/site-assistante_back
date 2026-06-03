<?php

namespace app\Infrastructure\Panel;

use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;
use Yii;

final class YiiRbacClientModuleMenuRepository implements ClientModuleMenuRepositoryInterface
{
    public function getModuleMenusForClient(int $publicKey): array
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByUser($publicKey);
        $orderModules = json_decode($_ENV['ORDER_MODULES'] ?? '[]', true) ?: [];

        uksort($permissions, static function ($key1, $key2) use ($orderModules) {
            $pos1 = array_search($key1, $orderModules, true);
            $pos2 = array_search($key2, $orderModules, true);

            return ($pos1 === false ? PHP_INT_MAX : $pos1) <=> ($pos2 === false ? PHP_INT_MAX : $pos2);
        });

        $menus = [];

        foreach ($permissions as $key => $permission) {
            if (!($auth->getChildren('accesses_modules')[$key] ?? false)) {
                continue;
            }

            $module = Yii::$app->getModule($key);

            if ($module !== null && isset($module->params['menu'])) {
                $menus[] = $module->params['menu'];
            }
        }

        return $menus;
    }
}
