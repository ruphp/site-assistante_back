<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingSectionRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_sections}}';
    }

    public function rules(): array
    {
        return [
            [['onboarding_id'], 'required'],
            [['onboarding_id', 'sort_order'], 'integer'],
            [['include_children', 'include_query', 'is_active'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['url'], 'string', 'max' => 500],
        ];
    }
}
