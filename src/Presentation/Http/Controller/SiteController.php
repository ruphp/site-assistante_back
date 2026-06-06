<?php

namespace app\Presentation\Http\Controller;

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
                'defaultClientId' => 'rsaa',
            ],
        ];
    }


    public function onAuthError($client)
    {
        Yii::$app->getSession()->setFlash('warning', 'Ошибка, вы не имеете доступ к системе администрирования помощника');
        //Yii::$app->user->logout();
        return $this->redirect('/logout');
    }

    public function onAuthSuccess($client)
    {
        if ($client->getId() !== 'rsaa') {
            return $this->onExternalAuthSuccess($client);
        }

        if (!is_null($client->roles)) {
            $roles = explode(',', $client->roles);
            $role = $roles[0] ?? 'no_oauth';

            $userIdentity = UserIdentity::findIdentityByAccessToken($role);

            if (!is_null($userIdentity)) { // добавляем внешний сервис аутентификации
                Yii::$app->user->login($userIdentity);
                return $this->redirect('/');
            }
        }
        Yii::$app->getSession()->setFlash('warning', 'Пользователь не имеет доступа к системе.  Запросите доступ к системе ЦИПП (cipp), выбрав нужную роль. <a target="_blank" href="'.$_ENV['RSAA_LK_HOST'].'">Личный кабинет РСАА</a>');
        return $this->redirect('/logout');
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
        if (!Yii::$app->user->isGuest) {
            if (!is_null(Yii::$app->authManager->getAssignments(Yii::$app->user->id)['admin'] ?? null)) {
                return $this->redirect('/admin');
            }
            elseif (!is_null(Yii::$app->authManager->getAssignments(Yii::$app->user->id)['manager'] ?? null)) {
                return $this->redirect('/manager');
            }
            Yii::$app->user->logout();
        }

        return $this->render('index');
    }

    public function actionLogin(): Response|string
    {
        if (Yii::$app->request->isPost)
            return $this->actionLoginPost();
        $userLoginForm = new UserLoginForm();

        // Разлогиниваем, если пользователь уже вошёл
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
/*        if ($_ENV['TYPE_DEPLOYED'] == 'MIRS') {
            $urlencode = urlencode($_ENV['RSAA_REDIRECT_URI']);
            $href = "{$_ENV['RSAA_AUTH_URL']}?client_id={$_ENV['RSAA_CLIENT']}&scope=openid&response_type=code&redirect_uri=$urlencode";
            return $this->redirect($href);
        }*/
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
            Yii::$app->session->setFlash('success', 'Email подтвержден. Теперь можно войти в панель.', false);

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
    }

    public function actionLogout(): Response
    {

        if (!Yii::$app->user->isGuest) {
            Yii::$app->user->logout();
            if ($_ENV['TYPE_AUTH'] == 'RSAA') {
                return $this->redirect($_ENV['RSAA_LOGOUT_URL'] . '?redirect_uri='.$_ENV['RSAA_LOGOUT_REDIRECT_URL']);

            }
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
