<?php

namespace app\Presentation\Http\Controller\api;

//ini_set('display_errors', 'On');

use app\Application\Panel\Metrics\PanelMetricsService;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

class ReportController extends ApiController
{
//апи методы ///////////////////////////////////////////////////////////////////////////////////////////////////

    public function actionUsage(): Response
    {
        $chart_filters['role'] = Yii::$app->request->get('role', 0);
        $chart_filters['start_date'] = Yii::$app->request->get('date_from');
        $chart_filters['end_date'] = Yii::$app->request->get('date_to');
        $chart_filters['usage_only_unic'] = (int)Yii::$app->request->get('usage_only_unic', 0);
        $chart_filters['type_period'] = Yii::$app->request->get('type_period', 'day');

        return $this->asJson($this->metrics()->usageData($chart_filters));

    }

    private function metrics(): PanelMetricsService
    {
        return Yii::$container->get(PanelMetricsService::class);
    }

}
