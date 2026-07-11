<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportTelegramManagerLinkRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_telegram_manager_links';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'public_key', 'chat_id', 'telegram_user_id'], 'required'],
            [['user_id', 'public_key', 'pending_conversation_id', 'is_active'], 'integer'],
            [['chat_id', 'telegram_user_id'], 'string', 'max' => 64],
            [['username', 'first_name', 'last_name'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }
}
