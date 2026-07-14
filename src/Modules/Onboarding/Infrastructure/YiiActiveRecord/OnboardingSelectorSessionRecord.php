<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingSelectorSessionRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_selector_sessions}}';
    }

    public function rules(): array
    {
        return [
            [['token', 'owner_public_key', 'public_key', 'target', 'created_at', 'expires_at'], 'required'],
            [['owner_public_key', 'public_key'], 'integer'],
            [['selector', 'page_url'], 'string'],
            [['created_at', 'selected_at', 'expires_at'], 'safe'],
            [['token'], 'string', 'max' => 64],
            [['target'], 'string', 'max' => 32],
            [['element_tag'], 'string', 'max' => 64],
            [['element_text'], 'string', 'max' => 255],
            [['token'], 'unique'],
        ];
    }
}
