<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;

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

    public function sendEmail()
    {
        /* @var $user Users */
        $user = Users::findOne(
            [
                'status' => 1,
                'email' => $this->email
            ]
        );

        if($user){
                $link = Html::a('Для смены пароля перейдите по этой ссылке.',
                    Yii::$app->urlManager->createAbsoluteUrl(
                        [
                            '/user/reset-password',
                            'key' => $user->passhash,
                            'user' => $user->id
                        ]
                    ));
                return Yii::$app->mailer->compose()
                    ->setTo($user->email)
                    ->setFrom(Yii::$app->params['adminEmail'] )
                    ->setSubject('Сброс пароля для '.$user->name)
                    ->setHtmlBody($this->bodyText($user->name, $link))
                    ->send();
            }

        return false;
    }


    public function bodyText($name, $link)
    {
        return "
<html>
<head> 
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">  
    <title>Смена пароля на smguide.ru!</title>  
</head>
<body> 
<p>Добрый день, $name !</p>
<p> $link </p>
-- <br>
Команда Smartius Guide</p>
<img style='width:200px;Height:57px' src='https://admin.smguide.ru/img/smguide_logo.png'>
</body> 
</html> ";
    }

}