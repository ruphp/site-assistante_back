<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportEntryPointRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_entry_points';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'title'], 'required'],
            [['public_key', 'priority', 'enabled', 'sort_order'], 'integer'],
            [['description'], 'string'],
            [['title'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }
}
