<?php

namespace app\Presentation\Http\Controller;

use app\Application\Panel\ClientPanelMenuService;
use Yii;
use yii\filters\AccessControl;

class ManagerController extends SmartiusController
{
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
                    ],
                ],
            ]
        ];

    }

    private function clientPanelMenu(): ClientPanelMenuService
    {
        return Yii::$container->get(ClientPanelMenuService::class);
    }

}
