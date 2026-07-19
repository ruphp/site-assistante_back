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
            [['public_key', 'enabled', 'polling_interval_seconds', 'keep_widget_open_when_online', 'auto_open_snooze_minutes', 'show_branding', 'ask_name', 'ask_email', 'ask_phone', 'notify_email', 'notify_telegram', 'notify_max'], 'integer'],
            [['welcome_message', 'offline_message', 'working_hours', 'auto_reply', 'notification_emails'], 'string'],
            [['work_schedule', 'holiday_schedule'], 'safe'],
            [['title', 'plan', 'timezone', 'telegram_bot_token', 'telegram_chat_id', 'max_api_url', 'max_bot_token', 'max_chat_id'], 'string', 'max' => 255],
        ];
    }
}

