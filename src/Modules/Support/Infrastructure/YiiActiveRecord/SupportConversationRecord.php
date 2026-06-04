<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportConversationRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_conversations';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'visitor_id', 'status'], 'required'],
            [['public_key'], 'integer'],
            [['visitor_id', 'visitor_email', 'visitor_ip', 'status'], 'string', 'max' => 255],
            [['page_url'], 'string', 'max' => 2048],
            [['created_at', 'updated_at', 'closed_at'], 'safe'],
        ];
    }
}
