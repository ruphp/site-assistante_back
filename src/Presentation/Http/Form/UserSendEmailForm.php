<?php

namespace app\Presentation\Http\Form;

use app\Infrastructure\YiiActiveRecord\Users;
use yii\base\Model;

class UserSendEmailForm extends Model
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'exist',
             'targetClass' => Users::className(),
/*             'filter' => [
                 'status' => User::STATUS_ACTIVE
             ],*/
             'message' => 'Данный емайл не зарегистрирован.'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'email' => 'Емайл'
        ];
    }

}
