<?php

namespace app\controllers\api;

//ini_set('display_errors', 'On');

use app\controllers\ApiController;
use app\helpers\ChartHelpers;
use Yii;

class ReportController extends ApiController
{


    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors'  => [
                // restrict access to
                'Origin'                         => ['*'],
                // Allow  methods
                'Access-Control-Request-Method'  => ['*'],
                // Allow only headers 'X-Wsse'
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Headers'   => ['*'],
                // Allow credentials (cookies, authorization headers, etc.) to be exposed to the browser
                //'Access-Control-Allow-Credentials' => true,
                // Allow OPTIONS caching
                'Access-Control-Max-Age'         => 3600,
                // Allow the X-Pagination-Current-Page header to be exposed to the browser.
                'Access-Control-Expose-Headers'  => ['*'],
            ],
        ];
        return $behaviors;
    }

//апи методы ///////////////////////////////////////////////////////////////////////////////////////////////////

    public function actionUsage(): false|array
    {
        $chart_filters['role'] = Yii::$app->request->get()['role'] ?? 0;
        $chart_filters['start_date'] = Yii::$app->request->get()['date_from'];
        $chart_filters['end_date'] = Yii::$app->request->get()['date_to'];
        $chart_filters['usage_only_unic'] = (int)Yii::$app->request->get()['usage_only_unic'] ?? 0;
        $chart_filters['type_period'] = Yii::$app->request->get()['type_period'] ?? 'day';
        $res = ChartHelpers::getDataChart('chart_usage', $chart_filters);
        return $res;

    }

}