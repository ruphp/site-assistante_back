<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 05.08.2015
 * Time: 15:46
 */

namespace app\Presentation\Http\Form;

use app\Application\User\Contract\UserAccountServiceInterface;
use Yii;
use yii\base\Model;
use yii\base\InvalidParamException;

class UserResetPasswordForm extends Model
{
    public $password;
    private string $key;
    private int $userId;

    public function rules()
    {
        return [
            ['password', 'required'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'password' => 'Пароль'
        ];
    }

    public function __construct($key,$user, $config = [])
    {
        if(empty($key) || !is_string($key))
            throw new InvalidParamException('Ключ не может быть пустым.');

        $this->key = $key;
        $this->userId = (int)$user;

        if(!$this->accountService()->canResetPassword($this->key, $this->userId))
            throw new InvalidParamException('Не верный ключ.');
        parent::__construct($config);
    }

    public function resetPassword()
    {
        return $this->accountService()->resetPasswordByToken($this->key, $this->userId, $this->password);
    }

    private function accountService(): UserAccountServiceInterface
    {
        return Yii::$container->get(UserAccountServiceInterface::class);
    }

}
