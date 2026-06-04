<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportSettingsRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_settings';
    }

    public function rules(): array
    {
        return [
            [['public_key'], 'required'],
            [['public_key', 'enabled', 'polling_interval_seconds', 'ask_name', 'ask_email', 'ask_phone', 'require_email_offline'], 'integer'],
            [['welcome_message', 'offline_message', 'contact_info', 'working_hours', 'auto_reply'], 'string'],
            [['title', 'timezone'], 'string', 'max' => 255],
        ];
    }
}
