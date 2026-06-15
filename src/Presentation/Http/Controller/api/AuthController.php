<?php

namespace app\Presentation\Http\Controller\api;

use Yii;
use yii\rest\Controller;
use yii\authclient\clients\Yandex;
use app\Presentation\Http\Form\UserLoginForm;
use app\Infrastructure\YiiActiveRecord\Users;

class AuthController extends Controller
{
    public function actionLogin()
    {
        $form = new UserLoginForm();
        $form->load($this->requestBody(), '');

        if ($form->validate() && $form->getUser()) {
            $user = $form->getUser();
            $token = Yii::$app->security->generateRandomString(64);

            // Храним токен -> user_id в кэше на 30 дней
            Yii::$app->cache->set('mobile_token:' . $token, $user->id, 86400 * 30);

            return [
                'success' => true,
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'public_key' => $user->public_key,
                ],
            ];
        }

        return [
            'success' => false,
            'message' => 'Неверный email или пароль',
        ];
    }

    public function actionYandex()
    {
        $body = $this->requestBody();
        $code = $body['code'] ?? null;

        if (!$code) {
            return ['success' => false, 'message' => 'Код не передан'];
        }

        $redirectUri = Yii::$app->urlManager->createAbsoluteUrl(['/site/auth', 'authclient' => 'yandex']);
        $client = new Yandex([
            'clientId' => $_ENV['YANDEX_OAUTH_CLIENT_ID'],
            'clientSecret' => $_ENV['YANDEX_OAUTH_CLIENT_SECRET'],
            'scope' => 'login:info login:email',
            'validateAuthState' => false,
            'normalizeUserAttributeMap' => [
                'email' => function ($attributes) {
                    return $attributes['email']
                        ?? $attributes['default_email']
                        ?? current($attributes['emails'] ?? [])
                        ?: null;
                },
            ],
        ]);

        $client->setReturnUrl($redirectUri);

        try {
            $client->fetchAccessToken($code);
            $info = $client->getUserAttributes();
        } catch (\Throwable $e) {
            Yii::error('Yandex info error: ' . $e->getMessage(), 'mobile');
            return [
                'success' => false,
                'message' => 'Не удалось получить профиль Яндекса',
                'debug' => YII_ENV_DEV ? $e->getMessage() : null,
            ];
        }

        Yii::error('Yandex info: ' . json_encode($info, JSON_UNESCAPED_UNICODE), 'mobile');

        $email = $info['email'] ?? $info['default_email'] ?? current($info['emails'] ?? []) ?: null;

        if (!$email) {
            Yii::error('Yandex info keys: ' . implode(',', array_keys((array)$info)), 'mobile');
            return [
                'success' => false,
                'message' => 'Яндекс не вернул email. Проверь права приложения login:email и повтори вход.',
                'debug' => YII_ENV_DEV ? $info : null,
            ];
        }

        $user = Users::findOne(['email' => $email]);
        if (!$user || $user->status !== Users::STATUS_ACTIVE) {
            return ['success' => false, 'message' => 'Пользователь не найден'];
        }

        $token = Yii::$app->security->generateRandomString(64);
        Yii::$app->cache->set('mobile_token:' . $token, $user->id, 86400 * 30);

        return [
            'success' => true,
            'token' => $token,
            'user' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email, 'public_key' => $user->public_key],
        ];
    }

    private function requestBody(): array
    {
        $rawBody = Yii::$app->request->getRawBody();
        $jsonBody = json_decode($rawBody, true);

        if (is_array($jsonBody)) {
            return $jsonBody;
        }

        return Yii::$app->request->post();
    }
}
