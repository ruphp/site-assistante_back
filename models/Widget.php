<?php

namespace app\models;

use app\modules\courses\models\Categories;
use Yii;
use yii\base\Model;

class Widget extends Model
{

    public array $params;
    public int $id_student = 0;
    public bool $is_role = false;
    public array $id_roles_system = [];// приходящие роли с сайта клиента
    public array $dop_modules = [];//разрешенные доп модули
    public array $widget_modules = [];//разрешенные доп модули для конфигурации виджета
    public array $id_roles = []; // id ролей по бд гайда
    public string $pathname = '/';
    public string $getparams = '';
    public int $ip;
    public int $public_key;

    public function fields()
    {
        $fields = parent::fields();

        // удаляем небезопасные поля
        unset($fields['text_contacts'], $fields['type_tickets']);

        return $fields;
    }

    public function rules()
    {
        return [
            [['public_key'], 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'public_key' => 'ID клиента',
        ];
    }

    public function __construct(int $public_key)
    {
        $this->public_key = $public_key;
        if ($params = Params::getWidgetParam($public_key)) {
            $this->params = $params;
            $this->params['widget_modules'][] = 'support';
            $this->params['run'] = $params['status'];





            if (!$this->pathname = Yii::$app->cache->get('cache_pathname_' . $this->pathname)) {
                $this->pathname = self::textClean($_REQUEST['pathname'] ?? '', 1, 0);
                Yii::$app->cache->set('cache_pathname_' . $this->pathname, $this->pathname);
            }



            if (!$this->getparams = Yii::$app->cache->get('cache_getparams_' . $this->getparams)) {
                $this->getparams = self::textClean($_REQUEST['getparams'] ?? '', 0, 1);
                Yii::$app->cache->set('cache_getparams_' . $this->getparams, $this->getparams);
            }



            if (isset($_REQUEST['userId'])) {
                if (!$this->id_student = Yii::$app->cache->get('cache_uuid_' .$this->public_key.'_'.$_REQUEST['userId'])) {
                    $this->id_student = $this->check_uuid($this->public_key, $_REQUEST['userId']);
                    Yii::$app->cache->set('cache_uuid_' .$this->public_key.'_'.$_REQUEST['userId'], $this->id_student);
                }

            }

            $ip = $_SERVER['REMOTE_ADDR'];
            $this->ip = ip2long($ip);

            $id_roles_system = isset($_REQUEST['userRole']) ? $_REQUEST['userRole'] : [];

            if (isset($_REQUEST['string_roles'])) { // для строки ролей, тк реакт отдает строку ролей
                $id_roles_system = explode(',', $_REQUEST['string_roles']);
            }

            $id_roles_system = array_map('intval', $id_roles_system);


            // работаем с ролями если они используются
            $this->is_role = Roles::find()
                ->where(['public_key' => $this->public_key])->cache(3600)
                ->count();
            if ($this->is_role && count($id_roles_system)) {

                $this->id_roles_system = $id_roles_system;
                $roles = Roles::find()
                    ->select('id,id_role_in_system')
                    ->where(['in', 'id_role_in_system', $this->id_roles_system])
                    ->andWhere(['public_key' => $this->public_key])->cache(3600)
                    ->asArray()->all();
                $this->id_roles_system = [];
                $this->id_roles = [];
                foreach ($roles as $role) {
                    $this->id_roles[] = (int)$role['id']; // вернем id ролей
                    $this->id_roles_system[] = (int)$role['id_role_in_system'];
                }
            }
            else {
                $this->id_roles_system = [];
            }
            $auth = Yii::$app->authManager;
            if (!$this->params['widget_modules'] = Yii::$app->cache->get('widget_modules_' . $public_key)) {
                $permissions = $auth->getPermissionsByUser($public_key);
                $this->params['widget_modules'] = [];
                foreach ($permissions as $key => $val) {

                    if ($auth->getChildren('accesses_modules')[$key] ?? false) {
                        $param = Yii::$app->getModule($key)->params;
                        $this->params['widget_modules'] = array_merge($this->params['widget_modules'], $param['widget_modules']);
                    }
                };
                Yii::$app->cache->set('widget_modules_' . $public_key, $this->params['widget_modules']);
            }
        }
        parent::__construct($this);
    }

    private static function textClean($text, $is_path = 0, $is_query = 0): string
    {
        if ($text == '') return '';
        $arr_ = [];
        if ($is_path) {

            $path = parse_url($text, PHP_URL_PATH);

            $arr = explode('/', $path);
            foreach ($arr as $val) {
                if (preg_match("/^[a-zа-яё\d_.-]*$/iu", $val) && !strpos($val, " ") && !strpos($val, "'") && !strpos($val, '"') && !empty($val)) {
                    $arr_[] = $val;
                }
            }
            $res = '/' . implode('/', $arr_);
        }
        if ($is_query) {
            $query = parse_url($text, PHP_URL_QUERY);
            $arr = explode('&', $query);

            foreach ($arr as $val) {
                if (preg_match("/^[a-zа-яё\d_=.-]*$/iu", $val) && !strpos($val, " ") && !strpos($val, "'") && !strpos($val, '"') && !empty($val)) {
                    $arr_[] = $val;
                }
            }
            $res = "";
            if (count($arr_)) {
                $res = "?";
            }
            $res .= implode('&', $arr_);
        }

        return $res;
    }

    private function check_uuid($pk, $id_student): int
    {
        $result = Params::find()->select('is_uuid')->where(['public_key' => $pk])->cache(3600)->one();
        $is_uuid = $result->is_uuid;
        if ($is_uuid) {
            $UUIDv4 = '/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i';
            if (!preg_match($UUIDv4, $id_student)) {
                return 0;
                //return $this->HTTPStatus(415, 'Not valid UUID');
            }
            $already = UserUuid::getUser($pk, $id_student);
            if (!isset($already)) {
                $user_uuid = new UserUuid();
                $user_uuid->uuid = $id_student;
                $user_uuid->public_key = $pk;
                if ($user_uuid->save()) {
                    return $user_uuid->id;
                }
                else {
                    //var_dump($user_uuid->errors);
                    return 0;
                }

            }
            return $already->id;
        }elseif(is_numeric($id_student)){
            return (int)$id_student;
        }
        return 0;
    }


}