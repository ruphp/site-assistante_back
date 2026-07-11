<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientPanelMenuService;
use app\Application\Panel\ClientProjectService;
use app\Application\Panel\ManagerOperatorService;
use app\Application\Role\Dto\RoleOperationResult;
use app\Application\Role\ManagerRoleService;
use app\Application\Panel\ManageAssistantSettingsService;
use app\Application\Panel\Metrics\PanelMetricsService;
use app\Modules\Support\Application\Bot\TelegramManagerBotService;
use app\Modules\Support\Application\Reporting\SupportUsageReportService;
use app\Presentation\Http\Controller\ManagerController;
use app\Presentation\Http\Form\ManagerOperatorForm;
use app\Presentation\Http\Form\ManagerOwnerContactForm;
use Exception;
use Yii;
use yii\web\UploadedFile;
use yii\web\Response;

class PanelController extends ManagerController
{
    public function __construct(
        $id,
        $module,
        private readonly ManageAssistantSettingsService $assistantSettings,
        private readonly ManagerRoleService $roles,
        private readonly PanelMetricsService $metrics,
        private readonly ClientPanelMenuService $panelMenu,
        private readonly ClientProjectService $projects,
        private readonly SupportUsageReportService $usageReport,
        private readonly ManagerOperatorService $operators,
        private readonly TelegramManagerBotService $telegramBot,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/profile');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();

        return $this->render('index', $this->projects->tabsData(
            $ownerPublicKey,
            (int)Yii::$app->request->get('projectId') ?: null,
        ));
    }

    public function actionProfile(): Response|string
    {
        $userId = (int)Yii::$app->user->id;
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        $form = $this->operators->profileForm($userId);

        if (Yii::$app->request->isPost && $form->load(Yii::$app->request->post())) {
            $form->avatar = UploadedFile::getInstance($form, 'avatar');
            if ($form->validate() && $this->operators->updateProfile($userId, $form)) {
                Yii::$app->session->setFlash('success', 'Профиль сохранён');

                return $this->redirect('/manager/profile');
            }
        }

        return $this->render('profile', [
            'form' => $form,
            'avatarUrl' => $this->operators->profileAvatarUrl($userId),
            'telegramCode' => $this->telegramBot->createLinkCode($userId, $ownerPublicKey),
        ]);
    }

    public function actionProjectCreate(): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $name = (string)Yii::$app->request->post('name', '');
        $domain = (string)Yii::$app->request->post('domain', '');

        if ($this->projects->create($ownerPublicKey, $name, $domain)) {
            Yii::$app->session->setFlash('success', 'Проект создан');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось создать проект');
        }

        return $this->redirect('/manager');
    }

    public function actionDesigne(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);

        if (Yii::$app->request->isPost) {
            try {
                if ($this->assistantSettings->saveDesign($publicKey, Yii::$app->request->post())) {
                    Yii::$app->session->setFlash('success', 'Настройки оформления сохранены');
                    return $this->redirect($this->projectUrl('/manager/designe', $projectId));
                }
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect($this->projectUrl('/manager/designe', $projectId));
            }

            Yii::$app->session->setFlash('error', 'Не удалось сохранить');
        }

        return $this->render(
            'designe',
            $this->assistantSettings->getDesignViewData($publicKey)->toArray()
            + $this->projects->tabsData($ownerPublicKey, $projectId),
        );
    }

    public function actionParams(): Response|string
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);

        if (Yii::$app->request->isPost) {
            if ($this->assistantSettings->saveParams($publicKey, Yii::$app->request->post())) {
                Yii::$app->session->setFlash('success', 'Настройки подключения сохранены');
                return $this->redirect($this->projectUrl('/manager/params', $projectId));
            }

            Yii::$app->session->setFlash('error', 'Ошибка');
        }

        return $this->render(
            'settings',
            $this->assistantSettings->getParamsViewData($publicKey)->toArray()
            + $this->projects->tabsData($ownerPublicKey, $projectId),
        );
    }

    public function actionLimits(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        return $this->render('limits', [
            'report' => $this->usageReport->clientReport(Yii::$app->user->identity->getPublicKey()),
        ]);
    }

    public function actionInstructions(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);

        return $this->render('instructions', [
            'publicKey' => $publicKey,
        ] + $this->projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionOperators(): Response|string
    {
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        if (!$this->operators->canManage($ownerPublicKey, (int)Yii::$app->user->id)) {
            Yii::$app->session->setFlash('error', 'Управление менеджерами доступно владельцу на платном тарифе');

            return $this->redirect('/manager');
        }

        $form = new ManagerOperatorForm();

        if (Yii::$app->request->isPost) {
            if ($form->load(Yii::$app->request->post())) {
                $form->avatar = UploadedFile::getInstance($form, 'avatar');
            }
            if ($form->validate()) {
                $password = $this->operators->create($ownerPublicKey, $form);
                if ($password !== null) {
                    Yii::$app->session->setFlash(
                        'success',
                        'Менеджер создан. Временный пароль: ' . $password
                    );

                    return $this->redirect('/manager/operators');
                }
            }

            Yii::$app->session->setFlash('error', 'Не удалось создать менеджера');
        }

        $operators = $this->operators->listForOwner($ownerPublicKey);
        $telegramCodes = [];
        foreach ($operators as $operator) {
            $telegramCodes[$operator->id] = $this->telegramBot->createLinkCode($operator->id, $ownerPublicKey);
        }

        return $this->render('operators', [
            'operators' => $operators,
            'form' => $form,
            'ownerForm' => $this->operators->ownerContactForm($ownerPublicKey),
            'operatorLimit' => $this->operators->operatorLimit($ownerPublicKey),
            'telegramCodes' => $telegramCodes,
        ]);
    }

    public function actionOperatorOwnerContacts(): Response
    {
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        if (!$this->operators->canManage($ownerPublicKey, (int)Yii::$app->user->id)) {
            Yii::$app->session->setFlash('error', 'Недостаточно прав');

            return $this->redirect('/manager');
        }

        $form = new ManagerOwnerContactForm();
        if ($form->load(Yii::$app->request->post())) {
            $form->avatar = UploadedFile::getInstance($form, 'avatar');
        }
        if ($form->validate() && $this->operators->updateOwnerContacts($ownerPublicKey, $form)) {
            Yii::$app->session->setFlash('success', 'Контакты владельца сохранены');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось сохранить контакты владельца');
        }

        return $this->redirect('/manager/operators');
    }

    public function actionOperatorResetPassword(): Response
    {
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        if (!$this->operators->canManage($ownerPublicKey, (int)Yii::$app->user->id)) {
            Yii::$app->session->setFlash('error', 'Недостаточно прав');

            return $this->redirect('/manager');
        }

        $operatorId = (int)Yii::$app->request->post('id', Yii::$app->request->get('id'));
        $password = $this->operators->resetPassword($ownerPublicKey, $operatorId);

        if ($password === null) {
            Yii::$app->session->setFlash('error', 'Не удалось сбросить пароль');
        } else {
            Yii::$app->session->setFlash(
                'success',
                'Новый пароль менеджера: ' . $password . '. Пароль также отправлен владельцу пуш-уведомлением.'
            );
        }

        return $this->redirect('/manager/operators');
    }

    public function actionOperatorDisable(): Response
    {
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        if (!$this->operators->canManage($ownerPublicKey, (int)Yii::$app->user->id)) {
            Yii::$app->session->setFlash('error', 'Недостаточно прав');

            return $this->redirect('/manager');
        }

        $operatorId = (int)Yii::$app->request->post('id', Yii::$app->request->get('id'));
        if ($this->operators->disable($ownerPublicKey, $operatorId)) {
            Yii::$app->session->setFlash('success', 'Менеджер отключен');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось отключить менеджера');
        }

        return $this->redirect('/manager/operators');
    }

    public function actionRoles(): Response|string
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $post = Yii::$app->request->post();
        $publicKey = Yii::$app->user->identity->getPublicKey();
        if (!$this->panelMenu->rolesEnabledForClient($publicKey)) {
            Yii::$app->session->setFlash('error', 'Управление ролями доступно на платном тарифе');

            return $this->redirect('/manager/params');
        }

        if (Yii::$app->request->isPost) {
            $result = $this->roles->saveFromPost($publicKey, $post);
            $this->setRoleOperationFlash($result);

            if ($result->status === RoleOperationResult::FORBIDDEN) {
                return $this->refresh();
            }

            return $this->redirect([null, 'tab' => 2]);
        }

        return $this->render('roles', $this->roles->getPageData($publicKey)->toArray());
    }

    public function actionRoleDelete($id = false): Response
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $publicKey = Yii::$app->user->identity->getPublicKey();
        if (!$this->panelMenu->rolesEnabledForClient($publicKey)) {
            Yii::$app->session->setFlash('error', 'Управление ролями доступно на платном тарифе');

            return $this->redirect('/manager/params');
        }

        $result = $this->roles->deleteById((int)$id, $publicKey);
        $this->setRoleOperationFlash($result);

        return $this->redirect(['/manager/roles']);
    }

    public function actionStatistics(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $charts = [];

        foreach (['usage'] as $chartName) {
            $categoriesAndSeries = $this->metrics->usageChart();
            $charts['names'][] = $chartName;
            $charts['html_chart'][$chartName] = $this->renderPartial('_chart_' . $chartName . '_html');
            $charts['js_chart'][$chartName] = $this->renderPartial(
                '_chart_' . $chartName . '_js',
                $categoriesAndSeries
            );
        }

        foreach ($this->metrics->moduleChartsForClient(Yii::$app->user->identity->getPublicKey()) as $moduleChart) {
            $charts['names'][] = $moduleChart->name;
            $charts['html_chart'][$moduleChart->name] = $this->renderPartial($moduleChart->htmlView);
            $charts['js_chart'][$moduleChart->name] = $this->renderPartial(
                $moduleChart->jsView,
                $moduleChart->data,
            );
        }

        return $this->render('statistics', $charts);
    }

    private function setRoleOperationFlash(RoleOperationResult $result): void
    {
        if ($result->status === RoleOperationResult::FORBIDDEN) {
            Yii::$app->session->setFlash('error', 'Правка чужого содержимого запрещена');
            return;
        }

        if ($result->status === RoleOperationResult::DELETED) {
            Yii::$app->session->setFlash('success', 'Роль удалена');
            return;
        }

        if (in_array($result->status, [RoleOperationResult::CREATED, RoleOperationResult::UPDATED], true)) {
            Yii::$app->session->setFlash('success', 'Роль сохранена');
            return;
        }

        Yii::$app->session->setFlash('error', 'Ошибка');
    }

    private function projectUrl(string $path, ?int $projectId): string
    {
        return $projectId === null ? $path : $path . '?projectId=' . $projectId;
    }

    private function isOwner(): bool
    {
        return (int)Yii::$app->user->identity->getId() === (int)Yii::$app->user->identity->getPublicKey();
    }
}
