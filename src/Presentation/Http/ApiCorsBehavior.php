<?php

namespace app\Presentation\Http;

use yii\filters\Cors;

final class ApiCorsBehavior
{
    public static function assistantApi(): array
    {
        return [
            'class' => Cors::class,
            'cors' => [
                'Origin' => ['*'],
                'Access-Control-Request-Method' => ['GET', 'POST', 'OPTIONS'],
                'Access-Control-Request-Headers' => [
                    'Origin',
                    'Content-Type',
                    'Accept',
                    'Authorization',
                    'X-Requested-With',
                ],
                'Access-Control-Allow-Headers' => [
                    'Origin',
                    'Content-Type',
                    'Accept',
                    'Authorization',
                    'X-Requested-With',
                ],
                'Access-Control-Max-Age' => 3600,
                'Access-Control-Expose-Headers' => [],
            ],
        ];
    }
}
