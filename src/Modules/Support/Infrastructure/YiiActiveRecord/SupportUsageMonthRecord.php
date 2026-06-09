<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportUsageMonthRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_usage_month';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'period_month'], 'required'],
            [['public_key', 'conversation_count', 'message_count'], 'integer'],
            [['period_month'], 'safe'],
        ];
    }
}
