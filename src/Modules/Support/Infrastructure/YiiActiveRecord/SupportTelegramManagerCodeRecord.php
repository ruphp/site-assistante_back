<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportTelegramManagerCodeRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_telegram_manager_codes';
    }

    public function rules(): array
    {
        return [
            [['user_id', 'public_key', 'code', 'expires_at'], 'required'],
            [['user_id', 'public_key'], 'integer'],
            [['code'], 'string', 'max' => 32],
            [['expires_at', 'used_at', 'created_at'], 'safe'],
        ];
    }
}
