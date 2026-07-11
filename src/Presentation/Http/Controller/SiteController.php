<?php

namespace app\Presentation\Http\Controller;

use app\Application\Panel\ClientProjectService;
use app\Application\User\Contract\UserAccountServiceInterface;
use app\Infrastructure\Security\YandexSmartCaptchaVerifier;
use app\Infrastructure\User\UserIdentity;
use app\Presentation\Http\Form\UserSendEmailForm;
use app\Presentation\Http\Form\UserJoinForm;
use app\Presentation\Http\Form\UserLoginForm;
use app\Infrastructure\YiiActiveRecord\Users;
use Yii;
use yii\db\Exception;
use yii\web\Response;

class SiteController extends SmartiusController
{
    public function __construct(
        $id,
        $module,
        private readonly UserAccountServiceInterface $userAccountService,
        private readonly ClientProjectService $projects,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actions(): array
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'auth' => [
                'class'           => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
                'cancelCallback' => [$this, 'onAuthError'],
            ],
        ];
    }

    public function actionYandexMobileCallback(): string
    {
        $code = (string)Yii::$app->request->get('code', '');
        $state = (string)Yii::$app->request->get('state', '');

        $scheme = 'sitewidget://oauth';
        $query = http_build_query(array_filter([
            'code' => $code !== '' ? $code : null,
            'state' => $state !== '' ? $state : null,
        ], static fn($value) => $value !== null && $value !== ''));

        $target = $scheme . ($query !== '' ? ('?' . $query) : '');
        $escaped = htmlspecialchars($target, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        return <<<HTML
<!doctype html>
<html lang="ru">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>SiteWidget</title>
  <script>
    window.location.replace("{$escaped}");
    setTimeout(function () {
      window.location.href = "{$escaped}";
    }, 250);
  </script>
</head>
<body>
  <p>Возврат в приложение...</p>
  <p><a href="{$escaped}">Открыть SiteWidget</a></p>
</body>
</html>
HTML;
    }


    public function onAuthError($client)
    {
        Yii::$app->getSession()->setFlash('warning', 'Ошибка, вы не имеете доступ к системе администрирования помощника');
        //Yii::$app->user->logout();
        return $this->redirect('/logout');
    }

    public function onAuthSuccess($client)
    {
        return $this->onExternalAuthSuccess($client);
    }

    /**
     * @throws \yii\base\Exception
     */
    private function onExternalAuthSuccess($client): Response
    {
        $attributes = $client->getUserAttributes();
        $email = $this->extractOAuthEmail($attributes);

        if ($email === null) {
            Yii::$app->session->setFlash('warning', 'Внешний сервис не передал email. Зарегистрируйтесь по email или разрешите доступ к email.');
            return $this->redirect('/login');
        }

        $userRecord = Users::findUserByEmail(mb_strtolower($email));

        if ($userRecord === null) {
            $userRecord = new Users();
            $userRecord->name = $this->extractOAuthName($attributes, $email);
            $userRecord->email = mb_strtolower($email);
            $userRecord->firm = $userRecord->name;
            $userRecord->public_key = null;
            $userRecord->setPassword(Users::gen_password(24));
            $userRecord->status = 1;

            if (!$userRecord->save()) {
                Yii::$app->session->setFlash('warning', 'Не удалось создать аккаунт через внешний сервис.');
                return $this->redirect('/login');
            }

            $this->assignManagerRole($userRecord);
        }

        $userIdentity = UserIdentity::findIdentity($userRecord->id);
        if ($userIdentity !== null) {
            Yii::$app->user->login($userIdentity);
        }

        return $this->redirect('/');
    }

    private function extractOAuthEmail(array $attributes): ?string
    {
        $email = $attributes['default_email'] ?? $attributes['email'] ?? null;

        if ($email === null && isset($attributes['emails']) && is_array($attributes['emails'])) {
            $email = $attributes['emails'][0] ?? null;
        }

        return is_string($email) && $email !== '' ? $email : null;
    }

    private function extractOAuthName(array $attributes, string $email): string
    {
        $name = $attributes['real_name']
            ?? $attributes['display_name']
            ?? trim(($attributes['first_name'] ?? '') . ' ' . ($attributes['last_name'] ?? ''));

        if (!is_string($name) || trim($name) === '') {
            $name = explode('@', $email)[0];
        }

        return mb_substr(trim($name), 0, 30);
    }
    public function actionIndex(): Response|string
    {
        return $this->render('index');
    }

    public function actionInstructions(): string
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/login');
        }

        return $this->redirect('/manager/instructions');
    }

    public function actionCmsPlugins(): string
    {
        return $this->render('cms-plugins');
    }

    public function actionLogin(): Response|string
    {
        if (Yii::$app->request->isPost)
            return $this->actionLoginPost();
        $userLoginForm = new UserLoginForm();

        // Р Р°Р·Р»РѕРіРёРЅРёРІР°РµРј, РµСЃР»Рё РїРѕР»СЊР·РѕРІР°С‚РµР»СЊ СѓР¶Рµ РІРѕС€С‘Р»
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        if (!Yii::$app->user->isGuest) {
            if (!is_null(Yii::$app->authManager->getAssignments(Yii::$app->user->id)['admin'] ?? null)) {
                return $this->redirect('/admin');
            }
            elseif (!is_null(Yii::$app->authManager->getAssignments(Yii::$app->user->id)['manager'] ?? null)) {
                return $this->redirect('/manager');
            }
            Yii::$app->user->logout();
        }
        return $this->render('login', compact('userLoginForm'));
    }

    public function actionSendEmail(): Response|string
    {
        $model = new UserSendEmailForm();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($this->userAccountService->sendPasswordResetEmail($model->email)):
                    Yii::$app->getSession()->setFlash('warning', 'Проверьте почту.');
                    return $this->goHome();
                else:
                    Yii::$app->getSession()->setFlash('error', 'Нельзя сбросить пароль.');
                endif;
            }
        }

        return $this->render('sendEmail', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     */
    public function actionJoin(): Response|string
    {
        if (Yii::$app->request->isPost){
            return $this->actionJoinPost();
        }

        // Разлогиниваем, если пользователь уже вошёл
        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }

        $userJoinForm = new UserJoinForm();
        $userJoinForm->setUsers();

        return $this->renderJoin($userJoinForm);
    }

    /**
     * @throws Exception
     * @throws \Exception
     */
    public function actionJoinPost(): Response|string
    {
        $userJoinForm = new UserJoinForm();
        if (
            $userJoinForm->load(Yii::$app->request->post())
            && $userJoinForm->validate()
            && $this->verifyJoinCaptcha()
        ) {
            $userRecord = new Users();
            $userRecord->setUserJoinForm($userJoinForm);
            $emailConfirmationEnabled = $this->isEmailConfirmationEnabled();
            if ($emailConfirmationEnabled) {
                $userRecord->prepareEmailConfirmation();
            }

            if ($userRecord->save()) {
                if ($emailConfirmationEnabled) {
                    $confirmUrl = Yii::$app->urlManager->createAbsoluteUrl([
                        '/site/confirm-email',
                        'token' => $userRecord->email_confirm_token,
                    ]);

                    if (!$this->userAccountService->sendEmailConfirmation($userRecord->name, $userRecord->email, $confirmUrl)) {
                        Yii::$app->session->setFlash('warning', 'Аккаунт создан, но письмо подтверждения не отправилось.', false);
                        return $this->renderJoin($userJoinForm);
                    }

                    Yii::$app->session->setFlash(
                        'success',
                        'Регистрация почти завершена. Проверьте почту и подтвердите email.',
                        false
                    );

                    return $this->redirect('/login');
                }

                if (!$this->sendJoinNotificationsIfEnabled($userJoinForm)) {
                    Yii::$app->session->setFlash('warning', 'Аккаунт создан, но приветственное письмо не отправилось.', false);
                    return $this->renderJoin($userJoinForm);
                }

                Yii::$app->session->setFlash(
                    'success',
                    'Вы успешно зарегистрировались, теперь можете авторизоваться на сайте',
                    false
                );
                $this->assignManagerRole($userRecord);
                return $this->redirect('/login');
            }
            else {
                Yii::$app->session->setFlash('warning', 'Произошла ошибка', false);
            }

        }
        return $this->renderJoin($userJoinForm);
    }

    public function actionConfirmEmail(string $token): Response
    {
        $userRecord = Users::findByEmailConfirmToken($token);

        if ($userRecord === null) {
            Yii::$app->session->setFlash('warning', 'Ссылка подтверждения недействительна или уже использована.', false);
            return $this->redirect('/login');
        }

        if ($userRecord->confirmEmail()) {
            $this->assignManagerRole($userRecord);
            Yii::$app->session->setFlash('success', 'Email подтверждён. Теперь можно войти в панель.', false);

            return $this->redirect('/login');
        }

        Yii::$app->session->setFlash('warning', 'Не удалось подтвердить email. Попробуйте позже.', false);

        return $this->redirect('/login');
    }

    private function renderJoin(UserJoinForm $userJoinForm): string
    {
        $captchaVerifier = new YandexSmartCaptchaVerifier();

        return $this->render('join', [
            'userJoinForm' => $userJoinForm,
            'captchaEnabled' => $captchaVerifier->shouldRenderWidget(),
            'captchaSiteKey' => $captchaVerifier->getSiteKey(),
            'oauthClients' => Yii::$app->authClientCollection->clients,
        ]);
    }

    private function verifyJoinCaptcha(): bool
    {
        $captchaVerifier = new YandexSmartCaptchaVerifier();
        $token = Yii::$app->request->post('smart-token');

        if ($captchaVerifier->verify(is_string($token) ? $token : null, Yii::$app->request->userIP)) {
            return true;
        }

        Yii::$app->session->setFlash('warning', 'Подтвердите, что вы не робот.', false);

        return false;
    }

    private function sendJoinNotificationsIfEnabled(UserJoinForm $userJoinForm): bool
    {
        $default = defined('YII_ENV_DEV') && YII_ENV_DEV ? '0' : '1';
        $isEnabled = filter_var($_ENV['REGISTRATION_SEND_EMAIL'] ?? $default, FILTER_VALIDATE_BOOLEAN);

        if (!$isEnabled) {
            return true;
        }

        return $this->userAccountService->sendJoinNotifications(
            $userJoinForm->name,
            $userJoinForm->email,
            $userJoinForm->password,
            Yii::$app->params['adminEmail'],
            $userJoinForm->subject_user_join,
            $userJoinForm->subject_admin_join,
            $userJoinForm->body_admin_join
        );
    }

    private function isEmailConfirmationEnabled(): bool
    {
        return filter_var($_ENV['EMAIL_CONFIRMATION_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN);
    }

    private function assignManagerRole(Users $userRecord): void
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole('manager');

        if ($role !== null && $auth->getAssignment('manager', $userRecord->id) === null) {
            $auth->assign($role, $userRecord->id);
        }

        if ((int)$userRecord->public_key !== (int)$userRecord->id) {
            $userRecord->public_key = $userRecord->id;
            $userRecord->save();
        }

        $this->projects->ensureOwnerProject(
            (int)$userRecord->public_key,
            (string)$userRecord->firm,
        );
    }

    public function actionLogout(): Response
    {

        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
        }
        return $this->redirect('/');
    }

    public function actionLoginPost(): string
    {
        $userLoginForm = new UserLoginForm();
        if ($userLoginForm->load(Yii::$app->request->post()) && $userLoginForm->validate()) {
            $userLoginForm->login();
            Yii::$app->session->setFlash('success', 'Успешно', false);
            $this->redirect('/');
        }
        return $this->render('login', compact('userLoginForm'));
    }

}
