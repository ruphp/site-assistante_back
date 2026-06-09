<?php

namespace app\Presentation\Http\Form;

use yii\base\Model;

class Designe extends Model
{
    public string $CustomCss = '';
    public string $LogoSvg = '';

    public function rules(): array
    {
        return [
            [['LogoSvg', 'CustomCss'], 'string'],
            ['LogoSvg', 'validateSvg'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'LogoSvg' => 'Загрузка логотипа (svg - максимальный размер 40*40 )',
            'CustomCss' => 'Дополнительные стили (css)',
        ];
    }

    public function validateSvg(string $attribute): void
    {
        $content = trim($this->$attribute);

        if ($content === '') {
            return;
        }

        if (!str_starts_with($content, '<svg') || !str_ends_with($content, '</svg>')) {
            $this->addError($attribute, 'Неверный формат svg');
        }
    }
}
