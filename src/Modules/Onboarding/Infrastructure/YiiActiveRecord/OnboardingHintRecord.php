<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingHintRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_hints}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'title'], 'required'],
            [['public_key', 'position', 'type', 'autostart', 'vision', 'instruction_id', 'button_instruction_id', 'left_offset', 'top_offset'], 'integer'],
            [['content'], 'string', 'max' => 500],
            [['type_bind', 'is_leftward', 'standalone_enabled', 'hide_after_view', 'is_active'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['selector'], 'string', 'max' => 1000],
            [['view_type', 'icon_type'], 'string', 'max' => 64],
            [['theme', 'trigger_type'], 'string', 'max' => 16],
            [['icon_color', 'background_color'], 'string', 'max' => 32],
            [['button_label'], 'string', 'max' => 80],
            [['button_url'], 'string', 'max' => 500],
        ];
    }
}
