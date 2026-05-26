<?php

namespace app\controllers;

use app\components\LeftMenu;
use app\helpers\ChartHelpers;
use Yii;
use yii\filters\AccessControl;

class ManagerController extends SmartiusController
{



    protected function leftMenu($lists){
        return LeftMenu::widget(['list' => ['manager/params' => 'Параметры', 'manager/designe' => 'Оформление', 'manager/roles' => 'Роли', 'manager/statistics' => 'Отчеты и аналитические панели',],'lists'=>$lists]);
    }

    public function behaviors(): array
    {
        Yii::$app->cache->flush();
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow'         => true,
                        'roles'         => ['manager'],
                        'matchCallback' => function () {


                            $auth = Yii::$app->authManager;
                            $permissions=$auth->getPermissionsByUser(Yii::$app->user->id);

                            uksort($permissions, function ($key1, $key2) {
                                $order_modules = json_decode($_ENV['ORDER_MODULES']);
                                $pos1 = array_search($key1, $order_modules);
                                $pos2 = array_search($key2, $order_modules);
                                return $pos1 - $pos2;
                            });
                            $lists = [];
                            foreach ($permissions as $key => $val) {
                                $param = Yii::$app->getModule($key)->params;
                                if ($auth->getChildren('accesses_modules')[$key]??false) {

                                        $lists[] = $param['menu'];


                                }
                            }

                            $this->leftMenu($lists);
                            return true;
                        },
                    ],
                ],
            ]
        ];

    }

}