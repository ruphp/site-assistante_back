<?php

namespace app\Presentation\Http\Controller;

use app\Application\Panel\ClientPanelMenuService;
use app\Presentation\Yii\Widget\LeftMenu;
use Yii;
use yii\filters\AccessControl;

class ManagerController extends SmartiusController
{
    protected function leftMenu(array $lists): string
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();
        $isOwner = (int)Yii::$app->user->identity->getId() === (int)$publicKey;

        return LeftMenu::widget([
            'list' => $this->clientPanelMenu()->baseMenu($publicKey, $isOwner),
            'lists' => $isOwner ? $lists : [],
        ]);
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
                            $publicKey = Yii::$app->user->identity->getPublicKey();
                            $this->leftMenu($this->clientPanelMenu()->moduleMenusForClient($publicKey));

                            return true;
                        },
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
