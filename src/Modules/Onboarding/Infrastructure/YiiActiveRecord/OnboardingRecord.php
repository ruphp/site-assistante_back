<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboardings}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'title'], 'required'],
            [['public_key', 'timeout', 'type', 'sort_order'], 'integer'],
            [['is_blur', 'auto_start', 'is_active'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }
}
