<?php

namespace app\Presentation\Http\Controller;

use app\Presentation\Http\ApiResponseFactory;
use app\Infrastructure\User\UserIdentity;
use app\Infrastructure\Assistant\Assistant;
use Yii;

class ApiController extends BaseApiController
{
    public Assistant $assistant;
    public $modelClass = "/";

    public static  function debug($arr,$exit = 0)// дебаг для контроллеров - $this->debug($arr);
    {
        echo '<pre>' . print_r($arr, true) . '</pre>';
        if ($exit){
            exit();
        }
    }
    public static function HTTPStatus($num, $text = false)
    {
        return (new ApiResponseFactory())->status((int)$num, $text);
    }

    protected function getIsPremissionByNamePublicKey($premission_name,$publicKey)
    {
        $auth = Yii::$app->authManager;
        $permissions = $auth->getPermissionsByUser($publicKey);
        foreach ($permissions as $key => $val) {
            if ($key == $premission_name) {
                return true;
            }
        }
        return false;
    }

    public static  function actionSystems(){
        $users = UserIdentity::find()->select(['name','firm'])->all();
        $result['type']=2;
        $result['els'][0]['type']='select';
        $result['els'][0]['name']='ATTR_ROLES';
        $result['els'][0]['required']=1;
        $result['els'][0]['text']='Выберите систему';
        foreach ($users as $user){
            $result['els'][0]['value'][$user->name]=$user->firm;
        }
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = $result;
        return $result;
    }
}
