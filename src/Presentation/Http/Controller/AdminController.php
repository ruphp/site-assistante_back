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
            ],
            'lists' => [
                '#' => [
                    'Отчеты и аналитические панели',
                    'admin/statistics' => 'Ключевая статистика',
                    'admin/grafana' => 'Технический мониторинг',
                    'admin/content_statistics' => 'Статистика по контенту',
                ]
            ],
        ]);
        return $behaviors;
    }
}
