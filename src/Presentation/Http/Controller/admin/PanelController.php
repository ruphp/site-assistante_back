<?php

namespace app\Presentation\Http\Controller\admin;

use app\Application\Admin\AdminClientService;
use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;
use app\Application\Admin\Monitoring\AdminMonitoringService;
use app\Modules\Support\Application\UseCase\OperatorSupportUseCase;
use app\Modules\Support\Application\Reporting\SupportUsageReportService;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionSettingsRecord;
use app\Infrastructure\YiiActiveRecord\Users;
use app\Presentation\Http\Controller\AdminController;
use app\Presentation\Http\Form\UserJoinForm;
use Exception;
use Yii;
use yii\data\Pagination;
use yii\filters\AccessControl;
use yii\web\Response;

class PanelController extends AdminController
{
    public function __construct(
        $id,
        $module,
        private readonly AdminClientService $clientService,
        private readonly AdminMonitoringService $monitoring,
        private readonly SupportUsageReportService $usageReport,
        private readonly OperatorSupportUseCase $operatorSupport,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionStatistics($chart_names = ['api_configurations', 'usage', 'open_widget']): string
    {
        $charts = [];

        foreach ($chart_names as $chart_name) {
            switch ($chart_name) {
                case 'api_configurations':
                    $categories_and_series = $this->monitoring->chart('api_configurations');
                    $charts['names'][] = 'chart_api_configurations';
                    $charts['html_chart_api_configurations'] = $this->getHTMLChart('api_configurations');
                    $charts['js_chart_api_configurations'] = $this->renderPartial(
                        '_chart_api_configurations_js',
                        $categories_and_series
                    );
                    break;

                case 'usage':
                    $categories_and_series = $this->monitoring->chart('usage');
                    $charts['names'][] = 'chart_usage';
                    $charts['html_chart_usage'] = $this->getHTMLChart('usage');
                    $charts['js_chart_usage'] = $this->renderPartial('_chart_usage_js', $categories_and_series);
                    break;

                case 'open_widget':
                    $categories_and_series = $this->monitoring->chart('open_widget');
                    $charts['names'][] = 'chart_open_widget';
                    $charts['html_chart_open_widget'] = $this->getHTMLChart('open_widget');
                    $charts['js_chart_open_widget'] = $this->renderPartial(
                        '_chart_open_widget_js',
                        $categories_and_series
                    );
                    break;
            }
        }

        return $this->render('statistics', $charts);
    }

    public function actionChart($name): false|Response
    {
        switch ($name) {
            case 'api_configurations':
                $chart_filters['start_date'] = Yii::$app->request->get('date_from');
                $chart_filters['end_date'] = Yii::$app->request->get('date_to');
                $chart_filters['only_unic'] = (int)Yii::$app->request->get('only_unic', 0);
                return $this->asJson($this->monitoring->dataChart('chart_api_configurations', $chart_filters));

            case 'open_widget':
                $chart_filters['start_date'] = Yii::$app->request->get('date_from');
                $chart_filters['end_date'] = Yii::$app->request->get('date_to');
                $chart_filters['open_widget_only_unic'] = (int)Yii::$app->request->get('open_widget_only_unic', 0);
                return $this->asJson($this->monitoring->dataChart('chart_open_widget', $chart_filters));

            case 'usage':
                $chart_filters['system'] = Yii::$app->request->get('system', 0);
                $chart_filters['role'] = 0;
                $chart_filters['start_date'] = Yii::$app->request->get('date_from');
                $chart_filters['end_date'] = Yii::$app->request->get('date_to');
                $chart_filters['usage_only_unic'] = (int)Yii::$app->request->get('usage_only_unic', 0);
                $chart_filters['type_period'] = Yii::$app->request->get('type_period', 'day');
                return $this->asJson($this->monitoring->dataChart('chart_usage', $chart_filters));

            case 'module_contents':
                $chart_filters['system'] = Yii::$app->request->get('system');
                return $this->asJson($this->monitoring->dataChart('chart_module_contents', $chart_filters));

            default:
                return false;
        }
    }

    public function getHTMLChart($chart_name, $chart_filters = []): string
    {
        switch ($chart_name) {
            case 'api_configurations':
                return $this->renderPartial('_chart_api_configurations_html', compact('chart_filters'));

            case 'module_contents':
                return $this->renderPartial('_chart_module_contents_html', compact('chart_filters'));

            case 'open_widget':
                return $this->renderPartial('_chart_open_widget_html', compact('chart_filters'));

            case 'usage':
                return $this->renderPartial('_chart_usage_html', compact('chart_filters'));

            default:
                return '';
        }
    }

    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionClients(): string
    {
        $clients = $this->usageReport->adminReports();
        $pages = new Pagination(['totalCount' => count($clients), 'pageSize' => 10]);

        return $this->render('clients', compact('clients', 'pages'));
    }

    public function actionLimits(): string
    {
        return $this->render('limits', [
            'reports' => $this->usageReport->adminReports(),
        ]);
    }

    public function actionInstructions(int $publicKey = 0): string
    {
        $query = InstructionArticleRecord::find()
            ->orderBy(['public_key' => SORT_ASC, 'id' => SORT_DESC]);
        if ($publicKey > 0) {
            $query->where(['public_key' => $publicKey]);
        }
        $articles = $query->all();
        $settings = InstructionSettingsRecord::find()
            ->indexBy('public_key')
            ->all();
        $clientPublicKeys = array_map('intval', Users::find()
            ->select('public_key')
            ->where(['status' => Users::STATUS_ACTIVE])
            ->andWhere(['not', ['public_key' => null]])
            ->distinct()
            ->column());

        return $this->render('instructions', compact('articles', 'settings', 'clientPublicKeys', 'publicKey'));
    }

    public function actionToggleInstructionBlock(int $id): Response
    {
        $article = InstructionArticleRecord::findOne($id);
        if ($article instanceof InstructionArticleRecord) {
            $article->admin_blocked = !$article->admin_blocked;
            $article->save(false, ['admin_blocked']);
            Yii::$app->session->setFlash('success', $article->admin_blocked ? 'Инструкция заблокирована' : 'Инструкция разблокирована');
        }

        return $this->redirect('/admin/instructions');
    }

    public function actionInstructionView(int $id): string|Response
    {
        $article = InstructionArticleRecord::findOne($id);
        if (!$article instanceof InstructionArticleRecord) {
            Yii::$app->session->setFlash('error', 'Инструкция не найдена');

            return $this->redirect('/admin/instructions');
        }

        $comments = InstructionArticleFeedbackRecord::find()
            ->where(['article_id' => $article->id])
            ->orderBy(['id' => SORT_DESC])
            ->limit(50)
            ->all();

        $client = Users::find()
            ->where(['public_key' => $article->public_key])
            ->orderBy(['id' => SORT_ASC])
            ->one();

        return $this->render('instruction-view', compact('article', 'comments', 'client'));
    }

    public function actionToggleInstructionCreation(int $publicKey): Response
    {
        $settings = InstructionSettingsRecord::findOne($publicKey);
        if (!$settings instanceof InstructionSettingsRecord) {
            $settings = new InstructionSettingsRecord(['public_key' => $publicKey]);
        }
        $settings->creation_locked = !$settings->creation_locked;
        $settings->save(false);
        Yii::$app->session->setFlash('success', $settings->creation_locked ? 'Создание инструкций заблокировано' : 'Создание инструкций разрешено');

        return $this->redirect('/admin/instructions');
    }

    public function actionResetDailyReplies(int $publicKey): Response
    {
        $this->usageReport->resetDailyOperatorRepliesForOwner((int)$publicKey);
        Yii::$app->session->setFlash('success', 'Лимит ответов за сегодня сброшен');

        return $this->redirect('/admin/clients/limits');
    }

    public function actionView($id = null): string|Response
    {
        $id = (int)($id ?? Yii::$app->request->get('id'));
        if ($id <= 0) {
            Yii::$app->session->setFlash('error', 'Клиент не найден');

            return $this->redirect('/admin/clients');
        }

        $data = $this->clientService->getUpdateViewData($id);
        $user = $data['user'];
        $publicKey = (int)($user?->public_key ?? $id);

        return $this->render('view', [
            'user' => $user,
            'report' => $this->usageReport->clientReport($publicKey),
        ]);
    }

    public function actionDialogs($publicKey = null, string $status = 'open'): string|Response
    {
        $publicKey = (int)($publicKey ?? Yii::$app->request->get('publicKey'));
        if ($publicKey <= 0) {
            Yii::$app->session->setFlash('error', 'Проект не найден');

            return $this->redirect('/admin/clients');
        }

        return $this->render('dialogs', [
            'publicKey' => $publicKey,
            'status' => $status,
            'conversations' => $this->operatorSupport->listConversations($publicKey, $status)->toArray()['conversations'] ?? [],
        ]);
    }

    public function actionDialog($publicKey = null, $conversationId = null): string|Response
    {
        $publicKey = (int)($publicKey ?? Yii::$app->request->get('publicKey'));
        $conversationId = (int)($conversationId ?? Yii::$app->request->get('conversationId'));
        if ($publicKey <= 0 || $conversationId <= 0) {
            Yii::$app->session->setFlash('error', 'Диалог не найден');

            return $this->redirect('/admin/clients');
        }

        return $this->render('dialog', [
            'publicKey' => $publicKey,
            'conversationId' => $conversationId,
            'conversation' => $this->operatorSupport->conversation($publicKey, $conversationId)->toArray()['conversation'] ?? [],
            'messages' => $this->operatorSupport->listMessages($publicKey, $conversationId)->toArray()['messages'] ?? [],
        ]);
    }

    public function actionJoin(): Response|string
    {
        $userJoinForm = new UserJoinForm();

        if ($userJoinForm->load(Yii::$app->request->post()) && $userJoinForm->validate()) {
            return $this->join($userJoinForm);
        }

        return $this->render('join', compact('userJoinForm'));
    }

    public function join(UserJoinForm $userJoin): Response
    {
        try {
            $this->clientService->createClient(CreateClientRequest::fromJoinForm($userJoin));
        } catch (Exception $e) {
            Yii::$app->session->setFlash('warning', 'Client was not created: ' . $e->getMessage(), false);
        }

        return $this->redirect('/admin/clients');
    }

    public function actionDelete($id): Response
    {
        $this->clientService->deleteClient((int)$id);

        Yii::$app->session->setFlash('success', 'Account deleted');
        return $this->redirect(['/admin/clients']);
    }

    public function actionUpdate($id = null): Response|string
    {
        $id = (int)($id ?? Yii::$app->request->get('id'));
        if ($id <= 0) {
            Yii::$app->session->setFlash('error', 'Клиент не найден');

            return $this->redirect('/admin/clients');
        }

        if (Yii::$app->request->post()) {
            try {
                $password = $this->clientService->updateClient(
                    UpdateClientRequest::fromPost((int)$id, Yii::$app->request->post())
                );

                $newPassword = $password === null ? '' : ' New password <b>' . $password . '</b>';
                Yii::$app->session->setFlash('success', 'Data changed.' . $newPassword, false);
            } catch (Exception $e) {
                Yii::$app->session->setFlash('warning', 'Client was not updated: ' . $e->getMessage(), false);
            }

            return $this->redirect('/admin/clients');
        }

        $data = $this->clientService->getUpdateViewData((int)$id);
        if ($data['user'] === null) {
            Yii::$app->session->setFlash('error', 'Клиент не найден');

            return $this->redirect('/admin/clients');
        }

        return $this->render('update', $data);
    }

    public function actionGrafana(): string
    {
        return $this->render('grafana');
    }

    public function actionContentStatistics($chart_names = ['module_contents']): string
    {
        $charts = [];

        foreach ($chart_names as $chart_name) {
            if ($chart_name !== 'module_contents') {
                continue;
            }

            $categories_and_series = $this->monitoring->chart('module_contents');
            $charts['names'][] = 'chart_module_contents';
            $charts['html_chart_module_contents'] = $this->getHTMLChart('module_contents');
            $charts['js_chart_module_contents'] = $this->renderPartial(
                '_chart_module_contents_js',
                $categories_and_series
            );
        }

        return $this->render('content_statistics', $charts);
    }

    public function behaviors(): array
    {
        parent::behaviors();

        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['admin'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    return $action->controller->redirect('/');
                },
            ],
        ];
    }
}
