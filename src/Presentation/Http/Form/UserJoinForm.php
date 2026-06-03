<?php
/**
 * User  - юзер (кто)
 * Join - регистрация (какой процесс)
 * Form - форма (к чему относится)
 * создаем модель для формы
 */

namespace app\Presentation\Http\Form;

use app\Infrastructure\YiiActiveRecord\Users;
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
            [['public_key'], 'integer'],
        ];
    }

    public function setUsers(?Users $userRecord = null)
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
