<?php

namespace app\Presentation\Http;

use Yii;
use yii\base\ActionFilter;
use app\Infrastructure\YiiActiveRecord\Users;

class MobileTokenAuth extends ActionFilter
{
    public function beforeAction($action)
    {
        $token = Yii::$app->request->headers->get('Authorization');
        $token = str_replace('Bearer ', '', $token);

        if (!$token) {
            throw new \yii\web\UnauthorizedHttpException('Токен не передан');
        }

        $userId = Yii::$app->cache->get('mobile_token:' . $token);
        if (!$userId) {
            throw new \yii\web\UnauthorizedHttpException('Токен недействителен');
        }

        $user = Users::findOne($userId);
        if (!$user) {
            throw new \yii\web\UnauthorizedHttpException('Пользователь не найден');
        }

        Yii::$app->user->login($user);
        return parent::beforeAction($action);
    }
}