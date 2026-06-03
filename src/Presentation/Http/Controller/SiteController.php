<?php

namespace app\Presentation\Http\Controller;

use app\Application\User\Contract\UserAccountServiceInterface;
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
    public function actionIndex(): Response|string
    {
        if (Yii::$app->request->isPost)
            return $this->actionLoginPost();
        $userLoginForm = new UserLoginForm();
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


        $userJoinForm = new UserJoinForm();
        $userJoinForm->setUsers();

        return $this->render('join', compact('userJoinForm'));
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
            && $this->userAccountService->sendJoinNotifications(
                $userJoinForm->name,
                $userJoinForm->email,
                $userJoinForm->password,
                Yii::$app->params['adminEmail'],
                $userJoinForm->subject_user_join,
                $userJoinForm->subject_admin_join,
                $userJoinForm->body_admin_join
            )
        ) {
            $userRecord = new Users();
            $userRecord->setUserJoinForm($userJoinForm);
            if ($userRecord->save()) {
                Yii::$app->session->setFlash(
                    'success',
                    'Вы успешно зарегистрировались, теперь можете авторизоваться на сайте',
                    false
                );
                //
                $auth = Yii::$app->authManager;
                $role = $auth->getRole('manager');
                $auth->assign($role, $userRecord->id);
                $userRecord->public_key = $userRecord->id;
                $userRecord->save();
                //
                return $this->redirect('/login');
            }
            else {
                Yii::$app->session->setFlash('warning', 'Произошла ошибка', false);
            }

        }
        return $this->render('join', compact('userJoinForm'));
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
