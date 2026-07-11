<?php

namespace app\Presentation\Http\Form;

use yii\base\Model;
use yii\web\UploadedFile;

final class ManagerOwnerContactForm extends Model
{
    public string $name = '';
    public string $phone = '';
    public string $telegram = '';
    public string $maxContact = '';
    public ?UploadedFile $avatar = null;

    public function rules(): array
    {
        return [
            ['name', 'required'],
            ['name', 'string', 'min' => 2, 'max' => 80],
            [['phone', 'telegram', 'maxContact'], 'string', 'max' => 128],
            [['name', 'phone', 'telegram', 'maxContact'], 'trim'],
            ['avatar', 'file', 'extensions' => ['png', 'jpg', 'jpeg', 'webp'], 'maxSize' => 2 * 1024 * 1024, 'skipOnEmpty' => true],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'name' => 'Имя для общения',
            'phone' => 'Телефон',
            'telegram' => 'Telegram',
            'maxContact' => 'MAX',
            'avatar' => 'Аватар',
        ];
    }
}
