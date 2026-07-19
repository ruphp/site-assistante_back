<?php

namespace app\Presentation\Http\Form;

use app\Infrastructure\YiiActiveRecord\Users;
use yii\base\Model;

final class ManagerOperatorForm extends Model
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $telegram = '';
    public string $maxContact = '';
    public array $projectIds = [];
    public $avatar = null;

    public function rules(): array
    {
        return [
            [['name', 'email'], 'required'],
            ['name', 'string', 'min' => 2, 'max' => 80],
            ['email', 'email'],
            ['email', 'validateEmailIsFree'],
            [['phone', 'telegram', 'maxContact'], 'string', 'max' => 128],
            ['projectIds', 'each', 'rule' => ['integer']],
            [['name', 'email', 'phone', 'telegram', 'maxContact'], 'trim'],
            ['avatar', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'webp'], 'maxSize' => 2 * 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }

    public function validateEmailIsFree(): void
    {
        if ($this->hasErrors('email')) {
            return;
        }

        if (Users::existsEmail($this->email)) {
            $this->addError('email', 'Этот email уже используется');
        }
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя оператора',
            'email' => 'Email для входа',
            'phone' => 'Телефон',
            'telegram' => 'Telegram',
            'maxContact' => 'MAX',
            'avatar' => 'Аватар',
            'projectIds' => 'Проекты',
        ];
    }
}
