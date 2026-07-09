<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportProjectRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_projects';
    }

    public function rules(): array
    {
        return [
            [['owner_public_key', 'public_key', 'name'], 'required'],
            [['owner_public_key', 'public_key', 'enabled', 'is_default'], 'integer'],
            [['name', 'domain'], 'string', 'max' => 255],
            [['created_at', 'updated_at'], 'safe'],
        ];
    }
}
