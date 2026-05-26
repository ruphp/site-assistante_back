<?php

namespace app\controllers\admin;

use app\controllers\AdminController;
use app\helpers\ChartHelpers;
use app\models\UserJoinForm;
use app\models\Users;
use Exception;
use Yii;
use yii\data\Pagination;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use yii\web\Response;

class PanelController extends AdminController
{
// указать нужные модули для вывода , при добалении нового тоже опрокинуть его в $chart_names

    public function actionStatistics($chart_names = ['api_configurations', 'usage', 'open_widget']): string
    {
        $charts = [];
        foreach ($chart_names as $chart_name) {
            switch ($chart_name) {
                case 'api_configurations':
                    $categories_and_series = ChartHelpers::getChart('api_configurations');
                    //debug($categories_and_series,1);
                    $js = $this->renderPartial('_chart_api_configurations_js', $categories_and_series);
                    $html = $this->getHTMLChart('api_configurations');
                    $charts['names'][] = 'chart_api_configurations';
                    $charts['html_chart_api_configurations'] = $html;
                    $charts['js_chart_api_configurations'] = $js;
                    break;
                case 'usage':
                    $categories_and_series = ChartHelpers::getChart('usage');
                    //debug($categories_and_series);
                    $js = $this->renderPartial('_chart_usage_js', $categories_and_series);
                    $html = $this->getHTMLChart('usage');
                    $charts['names'][] = 'chart_usage';
                    $charts['html_chart_usage'] = $html;
                    $charts['js_chart_usage'] = $js;
                    break;
                case 'open_widget':
                    $categories_and_series = ChartHelpers::getChart('open_widget');
                    $js = $this->renderPartial('_chart_open_widget_js', $categories_and_series);
                    //debug($js,1);
                    $html = $this->getHTMLChart('open_widget');
                    $charts['names'][] = 'chart_open_widget';
                    $charts['html_chart_open_widget'] = $html;
                    $charts['js_chart_open_widget'] = $js;
                    break;
                default;
            }
        }
        return $this->render('statistics', $charts);
    }

    public function actionChart($name): false|array
    {
        switch ($name) {
            case 'api_configurations':
                $chart_filters['start_date'] = Yii::$app->request->get()['date_from'];
                $chart_filters['end_date'] = Yii::$app->request->get()['date_to'];
                $chart_filters['only_unic'] = (int)Yii::$app->request->get()['only_unic'] ?? 0;
                $res = ChartHelpers::getDataChart('chart_api_configurations', $chart_filters);
                return $res;
            case 'open_widget':
                $chart_filters['start_date'] = Yii::$app->request->get()['date_from'];
                $chart_filters['end_date'] = Yii::$app->request->get()['date_to'];
                $chart_filters['open_widget_only_unic'] = (int)Yii::$app->request->get()['open_widget_only_unic'] ?? 0;
                $res = ChartHelpers::getDataChart('chart_open_widget', $chart_filters);
                return $res;
            case 'usage':
                $chart_filters['system'] = Yii::$app->request->get()['system'] ?? 0;
                $chart_filters['role'] = 0;
                $chart_filters['start_date'] = Yii::$app->request->get()['date_from'];
                $chart_filters['end_date'] = Yii::$app->request->get()['date_to'];
                $chart_filters['usage_only_unic'] = (int)Yii::$app->request->get()['usage_only_unic'] ?? 0;
                $chart_filters['type_period'] = Yii::$app->request->get()['type_period'] ?? 'day';
                $res = ChartHelpers::getDataChart('chart_usage', $chart_filters);
                return $res;
            case 'module_contents':
                $chart_filters['system'] = Yii::$app->request->get()['system'];
                $res = ChartHelpers::getDataChart('chart_module_contents', $chart_filters);
                return $res;
            default;
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
            default;
                return '';
        }
    }


    public function actionIndex(): string
    {
        return $this->render('index');
    }

    public function actionClients(): string
    {
        $list_users = Users::getListUsersManager();
        $users = $list_users;

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

    public function join($userJoin): Response
    {

        $user = new Users();
        try {
            $user->setUserJoinForm($userJoin);
            $user->save();

            $auth = Yii::$app->authManager;
            $role = $auth->getRole('manager');
            $auth->assign($role, $user->id);
        } catch (Exception $e) {
            Yii::$app->session->setFlash('warning', 'Ошибка, не удалось создать клиента ' . $e, false);
        }

        return $this->redirect('/admin/clients');
    }

    public function actionDelete($id): Response
    {
        $user = Users::findOne($id);
        if (!$user->status) {
            $user->delete();
        }

        Yii::$app->session->setFlash('success', 'Аккаунт удален');
        return $this->redirect(['/admin/clients']);
    }

    public function actionUpdate($id): Response|string
    {

        if (Yii::$app->request->post()) {
            $new_password = '';
            $is_shange_password = Yii::$app->request->post()['Users']['change_password'] ?? 0;
            $user = Users::findOne($id);
            $user->firm = Yii::$app->request->post()['Users']['firm'];
            $user->name = Yii::$app->request->post()['Users']['name'];
            $user->email = mb_strtolower(Yii::$app->request->post()['Users']['email']);
            $user->status = (int)Yii::$app->request->post()['Users']['status'];
            $user->gmt = (int)Yii::$app->request->post()['Users']['gmt'];

            $auth = Yii::$app->authManager;
            foreach (Yii::$app->request->post()['Users']['modules'] as $module => $is_access) {

                $permission = $auth->getPermission($module);
                if ((int)$is_access) {
                    try {
                        $auth->assign($permission, $id);
                    } catch (Exception) {
                        // роль уже есть
                    }

                } else {
                    $auth->revoke($permission, $id);
                }

            }

            if ($is_shange_password) {
                $password = Users::gen_password(8);
                $user->setPassword($password);
                $new_password = 'Новый пароль <b>' . $password . '</b>';
            }


            try {
                $user->save();

                Yii::$app->session->setFlash('success', 'Данные изменены. ' . $new_password, false);


            } catch (Exception $e) {

                Yii::$app->session->setFlash('warning', 'Ошибка, не удалось изменить данные ' . $e, false);
            }
            return $this->redirect('/admin/clients');
        }

        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByRole('accesses_modules');
        $user_modules = [];
        $name_modules = [];


        uksort($permissions, function ($key1, $key2) {
            $order_modules = json_decode($_ENV['ORDER_MODULES']);
            $pos1 = array_search($key1, $order_modules);
            $pos2 = array_search($key2, $order_modules);
            return $pos1 - $pos2;
        });

        foreach ($permissions as $module) {
            $user_modules[$module->name] = isset($auth->getPermissionsByUser($id)[$module->name]);
            $name_modules[$module->name] = $module->description;

        }

        /*        foreach (Yii::$app->getModules() as $key => $val) {
                    $param=Yii::$app->getModule($key)->params;

                    if (($param['dop_module']??false) ) {
                        $user_modules[$param['key']] = $user_modules[$param['key']]??false;
                        $name_modules[$param['key']] = $param['name'];
                    }
                }*/


        return $this->render('update', [
            'user' => Users::findOne($id), 'user_modules' => $user_modules, 'name_modules' => $name_modules,
        ]);
    }

    /**
     * @throws \yii\base\Exception
     * @throws Exception
     */


    public function actionGrafana(): string
    {
        return $this->render('grafana');
    }
    public function actionContentStatistics($chart_names = ['module_contents']): string
    {
        $charts = [];
        foreach ($chart_names as $chart_name) {
            switch ($chart_name) {
                case 'module_contents':
                    $categories_and_series = ChartHelpers::getChart('module_contents');
                    //debug($categories_and_series);
                    $js = $this->renderPartial('_chart_module_contents_js', $categories_and_series);
                    $html = $this->getHTMLChart('module_contents');
                    $charts['names'][] = 'chart_module_contents';
                    $charts['html_chart_module_contents'] = $html;
                    $charts['js_chart_module_contents'] = $js;
                    break;
                default;
            }
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