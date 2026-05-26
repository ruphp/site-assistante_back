<?php

namespace app\controllers\manager;

use app\controllers\ManagerController;
use app\helpers\ChartHelpers;
use app\helpers\ManagerHelpers;
use app\models\Designe;
use app\models\Params;
use app\models\Roles;
use Exception;
use ReflectionClass;
use Yii;
use yii\web\Response;

class PanelController extends ManagerController
{
    public function actionIndex(): string
    {
        return $this->render('index');
    }
    public function actionDesigne(): Response|string
    {
        $params = Yii::$app->user->identity->getParams()->asArray()->one();
        $designe = new Designe();
        if (!empty($params)) {
            $id = $params['id'];
            // обновить имеющуюся строку данных
            $params = Params::findOne($id);
            if ($params->load(Yii::$app->request->post())) {
                if(isset($_POST['Designe']['CustomCss'])){
                    $designe->setCustomCss($_POST['Designe']['CustomCss']);

                }
                if(isset($_POST['Designe']['LogoSvg'])){

                    $designe->setLogoSvg($_POST['Designe']['LogoSvg']);
                }
                if ($params->save()) {
                    Yii::$app->session->setFlash('success', 'Настройки оформления сохранены');
                    return $this->redirect('/manager/designe');// отменим повторную отправку при f5
                }
                else {
                    Yii::$app->session->addFlash('error', 'Не удалось сохранить');
                }
            }
        }
        else {
            // создать новую строку данных
            $params = new Params();
            ManagerHelpers::createFiles(Yii::$app->user->identity->getPublicKey());
            if ($params->load(Yii::$app->request->post())) {// если данные загружены

                Yii::$app->session->setFlash('warning', '111');
                if(isset($_POST['Designe']['CustomCss'])){
                    Yii::$app->session->addFlash('warning', 'css');
                    $designe->setCustomCss($_POST['Designe']['CustomCss']);

                }
                if(isset($_POST['Designe']['LogoSvg'])){

                    Yii::$app->session->addFlash('warning', 'svg');
                    $designe->setLogoSvg($_POST['Designe']['LogoSvg']);
                }

                if ($params->save()) {
                    //Yii::$app->session->setFlash('success', 'Настройки оформления сохранены');
                    return $this->redirect('/manager/designe');// отменим повторную отправку при f5
                }
                else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }



        return $this->render('designe', compact('params','designe'));
    }
    public function actionParams(): Response|string
    {
        ManagerHelpers::createFiles(Yii::$app->user->identity->getPublicKey());
        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }

//построение и редактирование параметров
        $params = Yii::$app->user->identity->getParams()->asArray()->one();
        if (!empty($params)) {
            $id = $params['id'];
            $params = Params::findOne($id);
            if ($params->load(Yii::$app->request->post())) {
                if ($params->save()) {
                    Yii::$app->session->setFlash('success', 'Настройки подключения сохранены');
                    return $this->redirect('/manager/params');
                }
                else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }
        else {
            // создать новую строку данных
            $params = new Params();
            
            if ($params->load(Yii::$app->request->post())) {
                if ($params->save()) {
                    Yii::$app->session->setFlash('success', 'Настройки подключения сохранены');
                    return $this->redirect('/manager/params');// отменим повторную отправку при f5
                }
                else {
                    Yii::$app->session->setFlash('error', 'Ошибка');
                }
            }
        }

        $code = $_ENV['ISINFOCOD']?
            $params::getCode(
                Yii::$app->user->identity->getPublicKey(),
                $_ENV['DOMAININFOAPIWIDGET'],
                $_ENV['DOMAININFOSTATICWIDGET'],
                $_ENV['DOMAININFOCUSTOMWIDGET'],
                $params,
                0):'';

        return $this->render('settings', compact('params', 'code'));
    }

    public function actionRoles()
    {

        if (Yii::$app->user->isGuest) {
            return $this->redirect('/user/login');
        }

        $roles = Yii::$app->user->identity->getRoles()->all();
        $newrole = new Roles();

        //редактирование ролей
        if (isset(Yii::$app->request->post()['Roles']['id'])) {
            $role = Roles::findOne(['id' => Yii::$app->request->post()['Roles']['id'], 'public_key' => Yii::$app->user->identity->getPublicKey()]);
            if (!empty($role)) {

                if (empty(Yii::$app->request->post()['Roles']['name'])) {// если имя пустое то удаляем
                    // todo изменить findmodel
                    if ($role->load(Yii::$app->request->post())
                        && Roles::find()->where(['id' => Yii::$app->request->post()['Roles']['id'],'public_key'=> Yii::$app->user->identity->getPublicKey()])->one()->delete()
                    ) {

                        Yii::$app->session->setFlash('success', 'Роль удалена');
                        return $this->redirect([null, 'tab' => 2]);// отменим повторную отправку при f5
                    }
                    else {
                        Yii::$app->session->setFlash('error', 'Ошибка');
                        return $this->redirect([null, 'tab' => 2]);// отменим повторную отправку при f5
                    }
                }
                else { // если имя есть то изменяем
                    if ($role->load(Yii::$app->request->post()) && $role->save()) {
                        Yii::$app->session->setFlash('success', 'Роль сохранена');
                        return $this->redirect([null, 'tab' => 2]);// отменим повторную отправку при f5
                    }
                    else {
                        Yii::$app->session->setFlash('error', 'Ошибка');
                        return $this->redirect([null, 'tab' => 2]);// отменим повторную отправку при f5
                    }
                }
            }
            else {
                Yii::$app->session->setFlash('error', 'Ай ай ай !!!! Правка чужого содержимого запрещена');
                return $this->refresh();// отменим повторную отправку при f5
            }
        }
        //создание ролей
        else {
            if ($newrole->load(Yii::$app->request->post()) && $newrole->save()) {
                Yii::$app->session->setFlash('success', 'Роль сохранена');
                return $this->redirect([null, 'tab' => 2]);// отменим повторную отправку при f5
            }
        }
        return $this->render('roles', compact('newrole', 'roles'));
    }

    public function actionRoleDelete($id = false)
    {

        if (Roles::find()->where(['id' => $id,'public_key'=> Yii::$app->user->identity->getPublicKey()])->one()->delete()) {
            Yii::$app->session->setFlash('success', 'Роль удалена');
        }
        else {
            Yii::$app->session->setFlash('warning', 'Роль не удалена');
        }

        $this->redirect(['/manager/roles']);

    }


    public function actionStatistics(): string
    {

        $charts = [];


        // подключим первый график  (пока 1)
        $chart_names = ['usage'];
        foreach ($chart_names as $chart_name) {
            switch ($chart_name) {
                case 'usage':
                    $categories_and_series = ChartHelpers::getChart($chart_name);
                    $js = $this->renderPartial('_chart_'.$chart_name.'_js', $categories_and_series);
                    $html = $this->renderPartial('_chart_'.$chart_name.'_html');
                    $charts['names'][] = $chart_name;
                    $charts['html_chart'][$chart_name] = $html;
                    $charts['js_chart'][$chart_name] = $js;
                    break;
                default;
            }
        }

        // подключим остальные блоки статистик модулей с данными по умолчанию
        $auth = Yii::$app->authManager;
        foreach($auth->getChildren('accesses_modules') as $key => $val){
            $param=Yii::$app->getModule($key)->params;
            foreach($param['charts'] as $chart_name){
                try {
                    $categories_and_series = $param['class_helpers']::getChart($chart_name)??[];
                    //if($chart_name === 'getChartSurveyLogs'){
                    //    debug($categories_and_series,1);
                    //}
                    $js = $this->renderPartial($param['dir_view'].'manager/panel/_chart_'.$chart_name.'_js', $categories_and_series);
                    $html = $this->renderPartial($param['dir_view'].'manager/panel/_chart_'.$chart_name.'_html');
                    $charts['names'][] = $chart_name;
                    $charts['html_chart'][$chart_name] = $html;
                    $charts['js_chart'][$chart_name] = $js;
                }catch (Exception $e){
                    if(YII_DEBUG) {
                        ManagerHelpers::debug([$e->getTrace()[0],$chart_name,$categories_and_series],1);
                    }
                    continue;

                }

            }
        }

        return $this->render('statistics', $charts);
    }

}