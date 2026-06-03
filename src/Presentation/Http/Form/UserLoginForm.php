<?php
/**
 * SmirnoVAV
 * Date: 20.11.2018
 * Time: 20:50
 */

namespace app\Presentation\Http\Form;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Infrastructure\User\UserIdentity;
use Yii;
use yii\base\Model;

class UserLoginForm extends Model
{
    public $email;
    public $password;
    public $remember;

    private $userRecord;

    public function rules()
    { // механизм валидации
        return [
            ['email', 'required', 'message' => 'Укажите email'],
            ['email', 'email', 'message' => 'email не по формату'],
            ['remember', 'boolean'],
            ['password', 'required', 'message' => 'Укажите пароль'],
            ['password', 'errorIfPasswordWrong'],
            ['email', 'errorIfEmailNotFound'],
        ];

    }

    public function errorIfPasswordWrong()
    {
        if ($this->hasErrors()) {
            return;
        }
        $this->userRecord = Users::findUserByEmail($this->email);// присваиваем текущему экземпляру пользователя данные из таблицы по емайл
        if (!is_null($this->userRecord)) {
            if (!$this->userRecord->validatePassword($this->password)) {
                $this->addError('password', 'Неправильный пароль');
            }
        }
    }

    public function errorIfEmailNotFound()
    {
        if (is_null($this->userRecord)) {
            $this->addError('email', 'Этот Email не зарегистрирован');
        }
    }

    public function login()
    {
        if ($this->hasErrors()) {
            return;
        }
        $userIdentity = UserIdentity::findIdentity($this->userRecord->id);
        Yii::$app->user->login($userIdentity,
            $this->remember ? 3600 * 24 * 30 : 0);

    }
}
