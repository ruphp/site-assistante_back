<?php

namespace app\Presentation\Http\Form;

use yii\base\Model;

final class ManagerOwnerContactForm extends Model
{
    public string $name = '';
    public string $phone = '';
    public string $telegram = '';
    public string $maxContact = '';

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 80],
            [['phone', 'telegram', 'maxContact'], 'string', 'max' => 128],
            [['name', 'phone', 'telegram', 'maxContact'], 'trim'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя для общения',
            'phone' => 'Телефон',
            'telegram' => 'Telegram',
            'maxContact' => 'MAX',
        ];
    }
}
