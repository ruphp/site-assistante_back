<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportOperatorProjectRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_operator_projects';
    }

    public function rules(): array
    {
        return [
            [['owner_public_key', 'operator_user_id', 'project_id'], 'required'],
            [['owner_public_key', 'operator_user_id', 'project_id'], 'integer'],
            [['created_at'], 'safe'],
        ];
    }
}
