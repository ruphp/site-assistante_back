<?php

namespace app\Presentation\Http\Controller\admin;

use app\Application\Admin\AdminClientService;
use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;
use app\Application\Admin\Monitoring\AdminMonitoringService;
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
        $users = $this->clientService->listClients();
        $pages = new Pagination(['totalCount' => count($users), 'pageSize' => 10]);

        return $this->render('clients', compact('users', 'pages'));
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

    public function actionUpdate($id): Response|string
    {
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

        return $this->render('update', $this->clientService->getUpdateViewData((int)$id));
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
