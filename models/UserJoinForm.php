<?php
/**
 * User  - юзер (кто)
 * Join - регистрация (какой процесс)
 * Form - форма (к чему относится)
 * создаем модель для формы
 */

namespace app\models;

use Baha2Odeh\RecaptchaV3\RecaptchaV3Validator;
use Yii;
use yii\base\Model;

/**
 *
 * @property mixed $userRecord
 */
class UserJoinForm extends Model // создаем список параметров, потом получаем эти параметры в контроллере
{
    public $name;
    public $email;
    public $firm;
    public $public_key = 0;
    public $password;
    public $password2;
    public $code;
    public $subject_user_join = 'Cпасибо за регистрацию на smguide.ru!';
    public $subject_admin_join = 'Регистрация на сайте';
    public $body_user_join = 'Вы успешно зарегистрировались на сайте Smguide.io';
    public $body_admin_join = 'На сайте Smguide.io успешно зарегистрировался новый пользователь';

    public function rules()
    { // механизм валидации
        return [
            ['name', 'required', 'message' => 'Укажите имя'],
            ['firm', 'required', 'message' => 'Укажите Вашу компанию'],
            ['name', 'string', 'min' => 3, 'max' => 30, 'tooShort' => 'минимум 3 символа', 'tooLong' => 'максимум 10 символов'],
            ['email', 'required', 'message' => 'Укажите email'],
            ['email', 'email', 'message' => 'email не по формату'],
            ['email', 'errorIfEmailUsed'],
            ['password', 'required', 'message' => 'Укажите пароль'],
            ['password', 'string', 'min' => 4, 'max' => 30, 'tooShort' => 'минимум 4 символа', 'tooLong' => 'максимум 10 символов'],
            ['password2', 'required', 'message' => 'Повторите пароль'],
            ['password2', 'compare', 'compareAttribute' => 'password', 'message' => 'Пароли не совпадают'],
            /*[['code'], RecaptchaV3Validator::className(), 'acceptance_score' => 0.3],*/
            [['public_key'], 'integer'],
        ];
    }

    public function setUsers(Users $userRecord = null)
    {
        //$userRecord->setTestUser();
        $this->name = '';
        $this->email = '';
        $this->password = $this->password2 = ''; // зададим всем пользователям один пароль
    }

    public function errorIfEmailUsed()
    {
        if (Users::existsEmail($this->email)) {
            $this->addErrors(['email' => 'Этот email уже используется']);
        }
    }

    public function gomail($email)
    {
        if(YII_ENV_DEV){ return true;}
        // отправим юзеру
        Yii::$app->mailer->compose()
            ->setTo($this->email) // куда отправить
            ->setFrom([$email => 'SMARTIUS GUIDE']) // почта админа
            ->setSubject($this->subject_user_join)
            ->setTextBody('тест')
            ->setHtmlBody($this->bodyText($this->name, $this->email, $this->password))
            ->send();
        // отправим adminu
        Yii::$app->mailer->compose()
            ->setTo($email) // куда отправить
            ->setFrom([$email => 'SMARTIUS GUIDE']) // почта админа
            ->setSubject($this->subject_admin_join)
            ->setTextBody($this->body_admin_join.' '.$this->name.' '.$this->email)
            ->send();

        return true;
    }

    public function bodyText($name, $email, $pass)
    {
        return "
<html>
<head> 
    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">  
    <title>Cпасибо за регистрацию на smguide.ru!</title>  
</head>
<body> 
<p>Добрый день, $name! Большое спасибо, что присоединились к Smartius Guide.</p>
<p></p>
<p></p>
<p>С чего начать?
<br>Первый шаг: 
<br>Авторизуйтесь на сайте для доступа в консоль администратора, используя ваши логин и пароль
<br>Логин: $email
<br>Пароль: указанный при регистрации 
<br>
<br>Второй шаг: 
<br>В административной панели в разделе «Настройки подключения» введите домен вашего сайта. Сформировавшийся скрипт скопируйте и разместите его на сайте, на нужных страницах перед закрывающим тегом ‹/body› 
<br>
<br>Готово! Теперь вы можете: 
<br>• Размещать контент для формирования базы знаний
<br>• Привлекать пользователей с помощью персонализированного взаимодействия с web-ресурсом 
<br>• Ускорять процесс внедрения новых функций и адаптации сотрудников
</p>
<p>
-- <br>
Команда Smartius Guide</p>
<img style='width:200px;Height:57px' src='https://admin.smguide.ru/img/smguide_logo.png'>
</body> 
</html> ";
    }

    public function attributeLabels()
    {
        if($_ENV['TYPE_AUTH'] == 'RSAA' ){
            return [
                'firm'         => 'Наименование ИС',
                'name'         => 'Наименование ИС латиницей (имя клиента в РСАА)',
            ];
        }
        return [
            'firm'         => 'Наименование организации',
            'name'         => 'Короткое наименование организации латиницей',
        ];

    }
}
/*Yii::$app->mailer->compose()
    ->setFrom('<fromUsername>@<yourDomain>')
    ->setTo('<user@Email>')
    ->setSubject('Уведемление с сайта <yourDomain>') // тема письма
    ->setTextBody('Текстовая версия письма (без HTML)')
    ->setHtmlBody('<p>HTML версия письма</p>')
    ->send();*/