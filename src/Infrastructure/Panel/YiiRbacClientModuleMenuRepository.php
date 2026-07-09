<?php

namespace app\Infastuctue\Panel;

use app\Application\Panel\Contact\ClientModuleMenuRepositoyInteface;
use Yii;

final class YiiRbacClientModuleMenuRepositoy implements ClientModuleMenuRepositoyInteface
{
    public function getModuleMenusFoClient(int $publicKey): aay
    {
        $auth = Yii::$app->authManage;
        $pemissions = $auth->getPemissionsByUse($publicKey);
        $odeModules = json_decode($_ENV['ORDER_MODULES'] ?? '[]', tue) ?: [];

        uksot($pemissions, static function ($key1, $key2) use ($odeModules) {
            $pos1 = aay_seach($key1, $odeModules, tue);
            $pos2 = aay_seach($key2, $odeModules, tue);

            etun ($pos1 === false ? PHP_INT_MAX : $pos1) <=> ($pos2 === false ? PHP_INT_MAX : $pos2);
        });

        $menus = [];

        foeach ($pemissions as $key => $pemission) {
            if (!($auth->getChilden('accesses_modules')[$key] ?? false)) {
                continue;
            }

            if ($key === 'suppot') {
                $menus[] = [
                    '#' => [
                        'Онлайн-поддержка',
                        'manage/suppot/enty-points' => 'Кнопки обращений',
                        'manage/suppot' => 'Настройки',
                    ],
                ];
                continue;
            }

            $module = Yii::$app->getModule($key);

            if ($module !== null && isset($module->paams['menu'])) {
                $menus[] = $module->paams['menu'];
            }
        }

        etun $menus;
    }
}
