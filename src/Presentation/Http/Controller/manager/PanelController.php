<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Role\Dto\RoleOperationResult;
use app\Application\Role\ManagerRoleService;
use app\Application\Panel\ManageAssistantSettingsService;
use app\Application\Panel\Metrics\PanelMetricsService;
use app\Presentation\Http\Controller\ManagerController;
use Exception;
use Yii;
use yii\web\Response;

class PanelController extends ManagerController
{
    public function __construct(
        $id,
        $module,
        private readonly ManageAssistantSettingsService $assistantSettings,
        private readonly ManagerRoleService $roles,
        private readonly PanelMetricsService $metrics,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionDesigne(): Response|string
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();

        if (Yii::$app->request->isPost) {
            try {
                if ($this->assistantSettings->saveDesign($publicKey, Yii::$app->request->post())) {
                    Yii::$app->session->setFlash('success', 'Настройки оформления сохранены');
                    return $this->redirect('/manager/designe');
                }
            } catch (Exception $e) {
                Yii::$app->session->setFlash('error', $e->getMessage());
                return $this->redirect('/manager/designe');
            }

            Yii::$app->session->setFlash('error', 'Не удалось сохранить');
        }

        return $this->render('designe', $this->assistantSettings->getDesignViewData($publicKey)->toArray());
    }

    public function actionParams(): Response|string
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }

        $publicKey = Yii::$app->user->identity->getPublicKey();

        if (Yii::$app->request->isPost) {
            if ($this->assistantSettings->saveParams($publicKey, Yii::$app->request->post())) {
                Yii::$app->session->setFlash('success', 'Настройки подключения сохранены');
                return $this->redirect('/manager/params');
            }

            Yii::$app->session->setFlash('error', 'Ошибка');
        }

        return $this->render('settings', $this->assistantSettings->getParamsViewData($publicKey)->toArray());
    }

    public function actionRoles(): Response|string
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }

        $post = Yii::$app->request->post();
        $publicKey = Yii::$app->user->identity->getPublicKey();

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

        $result = $this->roles->deleteById((int)$id, Yii::$app->user->identity->getPublicKey());
        $this->setRoleOperationFlash($result);

        return $this->redirect(['/manager/roles']);
    }

    public function actionStatistics(): string
    {
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
}
