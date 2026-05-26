<?php

namespace app\controllers;

use yii\web\Controller;

class SmartiusController extends Controller
{
    public function debug($arr,$exit=false): void// дебаг для контроллеров - $this->debug($arr);
    {
        echo '<pre>' . print_r($arr, true) . '</pre>';
        if($exit){
            exit;
        }
    }
    public static function HTTPStatus($num, $text = false)
    {
        $http = [
            100 => $text ?? 'Continue',
            101 => $text ?? 'Switching Protocols',
            200 => $text ?? 'OK',
            201 => $text ?? 'Created',
            202 => $text ?? 'Accepted',
            203 => $text ?? 'Non-Authoritative Information',
            204 => $text ?? 'No Content',
            205 => $text ?? 'Reset Content',
            206 => $text ?? 'Partial Content',
            300 => $text ?? 'Multiple Choices',
            301 => $text ?? 'Moved Permanently',
            302 => $text ?? 'Found',
            303 => $text ?? 'See Other',
            304 => $text ?? 'Not Modified',
            305 => $text ?? 'Use Proxy',
            307 => $text ?? 'Temporary Redirect',
            400 => $text ?? 'Bad Request',
            401 => $text ?? 'Unauthorized',
            402 => $text ?? 'Payment Required',
            403 => $text ?? 'Forbidden',
            404 => $text ?? 'Not Found',
            405 => $text ?? 'Method Not Allowed',
            406 => $text ?? 'Not Acceptable',
            407 => $text ?? 'Proxy Authentication Required',
            408 => $text ?? 'Request Time-out',
            409 => $text ?? 'Conflict',
            410 => $text ?? 'Gone',
            411 => $text ?? 'Length Required',
            412 => $text ?? 'Precondition Failed',
            413 => $text ?? 'Request Entity Too Large',
            414 => $text ?? 'Request-URI Too Large',
            415 => $text ?? 'Unsupported Media Type',
            416 => $text ?? 'Requested Range Not Satisfiable',
            417 => $text ?? 'Expectation Failed',
            422 => $text ?? 'already error',
            500 => $text ?? 'Internal Server Error',
            501 => $text ?? 'Not Implemented',
            502 => $text ?? 'Bad Gateway',
            503 => $text ?? 'Service Unavailable',
            504 => $text ?? 'Gateway Time-out',
            505 => $text ?? 'HTTP Version Not Supported',
        ];
        if ($num > 300) {
            $json = [
                'code'  => $num,
                'error' => $http[$num],
            ];
        }
        else {
            $json = [
                'code'     => $num,
                'response' => $http[$num],
            ];
        }

        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $json;
        $response->setStatusCode($num);
        return $json;
    }



}
