<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportMessageRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_messages';
    }

    public function rules(): array
    {
        return [
            [['conversation_id', 'public_key', 'sender_type', 'body'], 'required'],
            [['conversation_id', 'public_key'], 'integer'],
            [['body'], 'string'],
            [['sender_type', 'sender_id'], 'string', 'max' => 255],
            [['created_at'], 'safe'],
        ];
    }
}
