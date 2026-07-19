<?php

namespace app\Presentation\Http;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Infrastructure\User\UserIdentity;
use Yii;
use yii\base\ActionFilter;
use yii\web\Response;

class MobileTokenAuth extends ActionFilter
{
    public function beforeAction($action)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $header = (string)Yii::$app->request->headers->get('Authorization', '');
        $token = '';

        if ($header !== '') {
            $token = preg_replace('/^Bearer\s+/i', '', $header) ?? '';
        }

        if (!$token) {
            throw new \yii\web\UnauthorizedHttpException('Токен не передан');
        }

        $user = Users::find()
            ->where([
                'mobile_auth_token' => $token,
                'status' => Users::STATUS_ACTIVE,
            ])
            ->one();

        if (!$user) {
            throw new \yii\web\UnauthorizedHttpException('Токен недействителен');
        }

        $identity = UserIdentity::findIdentity($user->id);
        if (!$identity) {
            throw new \yii\web\UnauthorizedHttpException('Токен недействителен');
        }

        Yii::$app->user->login($identity);
        return parent::beforeAction($action);
    }
}
