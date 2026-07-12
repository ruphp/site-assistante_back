<?php

namespace app\Presentation\Http\Controller;

use app\Presentation\Yii\Widget\LeftMenu;
use Yii;

class AdminController extends SmartiusController
{
    /**
     * @throws
     */
    public function behaviors(): array
    {
        Yii::$app->cache->flush();
        $behaviors = parent::behaviors();

        LeftMenu::widget([
            'list' => [
                'admin/clients' => 'Панель администратора',
                'admin/clients/limits' => 'Лимиты клиентов',
            ],
            'lists' => [],
        ]);
        return $behaviors;
    }
}
