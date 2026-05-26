<?php

namespace app\controllers\api;

//ini_set('display_errors', 'On');

use app\controllers\ApiController;
use app\models\Widget;
use OpenApi\Attributes as OA;
use Yii;

class WidgetController extends ApiController
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

    public function actionConfiguration($publicKey): array
    {

        $this->widget = new Widget((int)$publicKey);

   


        $position = $this->widget->params['leftbutton'] ? "left" : "right";

        //  если стоит чекбокс отображать текст для контактов
        $text_contacts = $this->widget->params['tab_tp_contacts'] ? $this->widget->params['tp_contacts'] : "";

        $arr_domains = explode(",", $this->widget->params['domain']);

        foreach ($arr_domains as $val) {
            $arr_hosts[] = parse_url($val)['host'] ?? '';

        }
        $ticket_types = [
            1 =>'tpsmartius',
            2 =>'tpotrs'
        ];
        $ticket_type = array_intersect($ticket_types, $this->widget->params['widget_modules']);

        $support = count($ticket_type)?array_key_first($ticket_type):0;

        $modules = $this->widget->params['widget_modules'];
        if($support){
            $name_support =  $ticket_types[$support];
            unset($modules[array_search($name_support,$modules)]);
            $modules[] = 'support';
        }






        $conf = [
            "error"          => [],
            "position"       => $position,
            "run"            => $this->widget->params['run'],
            "theme"            => $this->widget->params['design'],
            "domain"         => $arr_hosts,
            "type_tickets"   => $support,//  тип ТП   0 - откл  , 1 - встроеная , 2 - СУИ
            "text_contacts"  => $text_contacts, // текст для контактов
            "zero_log_delay" => $this->widget->params['timeout'], // таймаут
            "url_smguide_tp" => $this->widget->params['server_stp'], //урл тп smguide
            "modules" =>  array_values($modules)

        ];
//$publicKey,$userRole=[],$userId
        $log = [
            "date"      => date("Y-m-d H:i:s"),
            "userId"    => $this->widget->id_student,
        ];

        try {

            Yii::$app->redis->lpush('log/configuration/'.(int)$publicKey, json_encode($log));
            //echo Yii::$app->redis->rpop('log/configuration/'.(int)$publicKey);

        } catch (\Exception $e) {
            //todo действия при поломке редиса
            //наверное напрямую в бд надо будет заносить в правки
            //var_dump($e);
            $conf["error"]["redis"] = 'no connect';
        }


        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $conf;

    }


    public function actionLogOpen($publicKey): array
    {

        $this->widget = new Widget((int)$publicKey);



        $log = [
            "date"   => date("Y-m-d H:i:s"),
            "userId" => $this->widget->id_student,
            "userRoles" => $this->widget->id_roles_system,
        ];



        try {

            $res = Yii::$app->redis->lpush('log/open/' . (int)$publicKey, json_encode($log));
            //echo Yii::$app->redis->rpop('log/open/'.(int)$publicKey);

        } catch (\Exception $e) {
            //todo действия при поломке редиса
            return $this->HTTPStatus(504, 'Redis no connect '.$e->getMessage());
        }

        return $this->HTTPStatus(200, 'Ok id='.$res);

    }

}