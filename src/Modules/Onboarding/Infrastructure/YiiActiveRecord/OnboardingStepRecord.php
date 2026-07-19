<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingStepRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_steps}}';
    }

    public function rules(): array
    {
        return [
            [['section_id'], 'required'],
            [['section_id', 'hint_id', 'position', 'sort_order'], 'integer'],
            [['text'], 'string'],
            [['is_active'], 'boolean'],
            [['selector'], 'string', 'max' => 1000],
        ];
    }
}
