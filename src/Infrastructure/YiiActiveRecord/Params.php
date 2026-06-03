<?php

namespace app\Infrastructure\YiiActiveRecord;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "params".
 *
 * @property int $id
 * @property int $public_key
 * @property string $design
 * @property string $domain
 * @property int $run
 * @property int $tab_tickets
 * @property int $leftbutton
 * @property string $default_answer
 * @property int $timeout
 * @property int $is_uuid
 */


class Params extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'params';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['public_key'], 'required'],
            [['public_key', 'run',  'tab_tickets', 'tab_tp_contacts' , 'leftbutton',  'timeout', 'is_uuid','chatbot_bigdata_system_id','chatbot_bigdata_is_active'], 'integer'],
            [['design', 'tp_contacts',  'token_stp', 'server_stp','domain'], 'string', 'max' => 500],
            [['default_answer'], 'string', 'max' => 255]
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id'                 => 'ID',
            'public_key'         => 'Порядковый номер ИС',
            'color'              => 'Color',
            'domain'             => 'URL сайта',
            'run'                => 'Run',
            'tab_tp_contacts'    => 'Отображать контакты ТП',
            'tp_contacts'        => 'Контакты ТП',
            'design'         => 'Выбор темы',
            'leftbutton'         => 'Расположение кнопки по горизонтали',
            'default_answer'     => 'Приветственное сообщение по умолчанию, для чат ботов',
            'timeout'            => 'Таймаут запуска действий',
            'is_uuid'            => 'Используется ли uuid для пользователей сайта',
            'chatbot_bigdata_system_id'            => 'Укажите id системы в апи Большие данные',
            'chatbot_bigdata_is_active'            => 'Использовать ИИ Большие данные в чат-ботах',
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if ($this->getIsNewRecord()) {
            return $this->insert($runValidation, $attributeNames);
        }
        else {
            return $this->update($runValidation, $attributeNames) !== false;
        }
    }

    public function beforeSave($insert)
    {
        Yii::warning($insert, 'before else');
        // если $insert== true значит, метод вызвался при создании записи, иначе при обновлении
        $saveContinue = parent::beforeSave($insert); // если $saveContinue == false, сохранение будет отменено









        if (empty(trim((string)$this->tp_contacts))) {
            $this->tp_contacts = null;
        }



        return $saveContinue;
    }



    public static function getCode($user, $domain, $domainstatic, $domaincustom)
    {

            $code =
                "&lt;script&gt;
    window.Smartius = {
        apiUrl: '" . $domain . "/api',
        staticUrl: '" .$domainstatic. "',
        customUrl: '" .$domaincustom. "',
        publicKey: " . $user . ",
        _user: {
            id: null,
            role: null,
            name: null,
            email: null
        }
    };
    var script = document.createElement('script');
    script.src = '$domainstatic/lib.js', document.head.appendChild(script);
&lt;/script&gt;";

        return $code;
    }

    public static function getWidgetParam($id): array|ActiveRecord|null
    {
        return self::find()
            ->select('params.*,users.status')
            ->innerJoin('users', 'users.id = params.public_key')
            ->where(['users.public_key' => $id])->cache(3600)
            ->asArray()->one();
    }



}
