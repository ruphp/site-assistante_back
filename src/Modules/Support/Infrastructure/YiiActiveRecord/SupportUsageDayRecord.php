<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportUsageDayRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_usage_day';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'period_day'], 'required'],
            [['public_key', 'operator_reply_count'], 'integer'],
            [['period_day', 'created_at', 'updated_at'], 'safe'],
        ];
    }
}
