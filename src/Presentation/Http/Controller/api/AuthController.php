<?php

namespace app\Presentation\Http\Controller\api;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Presentation\Http\Form\UserLoginForm;
use Yii;
use yii\authclient\clients\Yandex;
use yii\rest\Controller;

class AuthController extends Controller
{
    public function actionYandexUrl(): array
    {
        $body = $this->requestBody();
        $redirectUri = trim((string)($body['redirectUri'] ?? ''));
        if ($redirectUri === '') {
            $redirectUri = Yii::$app->urlManager->createAbsoluteUrl(['/site/yandex-mobile-callback']);
        }

        $state = Yii::$app->security->generateRandomString(24);
        Yii::$app->cache->set('yandex_oauth_state:' . $state, [
            'redirectUri' => $redirectUri,
        ], 600);

        $query = http_build_query([
            'response_type' => 'code',
            'client_id' => $_ENV['YANDEX_OAUTH_CLIENT_ID'],
            'redirect_uri' => $redirectUri,
            'scope' => 'login:info login:email',
            'force_confirm' => 'yes',
            'state' => $state,
        ]);

        return [
            'success' => true,
            'authUrl' => 'https://oauth.yandex.ru/authorize?' . $query,
            'redirectUri' => $redirectUri,
            'state' => $state,
        ];
    }

    public function actionLogin(): array
    {
        $form = new UserLoginForm();
        $form->load($this->requestBody(), '');

        if ($form->validate() && $form->getUser()) {
            $user = $form->getUser();

            return [
                'success' => true,
                'token' => $this->issueMobileToken($user),
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

    public function actionYandex(): array
    {
        $body = $this->requestBody();
        $code = $body['code'] ?? null;
        $redirectUri = trim((string)($body['redirectUri'] ?? ''));
        $state = trim((string)($body['state'] ?? ''));

        if (!$code) {
            return ['success' => false, 'message' => 'Код не передан'];
        }

        if ($redirectUri === '') {
            $redirectUri = Yii::$app->urlManager->createAbsoluteUrl(['/site/yandex-mobile-callback']);
        }

        if ($state === '') {
            return ['success' => false, 'message' => 'Не передано состояние авторизации'];
        }

        $stateData = Yii::$app->cache->get('yandex_oauth_state:' . $state);
        if (!is_array($stateData) || (($stateData['redirectUri'] ?? null) !== $redirectUri)) {
            return ['success' => false, 'message' => 'Неверное состояние авторизации'];
        }

        Yii::$app->cache->delete('yandex_oauth_state:' . $state);

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

        return [
            'success' => true,
            'token' => $this->issueMobileToken($user),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'public_key' => $user->public_key,
            ],
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

    private function issueMobileToken(Users $user): string
    {
        $token = Yii::$app->security->generateRandomString(64);
        $user->mobile_auth_token = $token;
        $user->save(false, ['mobile_auth_token']);

        return $token;
    }
}
