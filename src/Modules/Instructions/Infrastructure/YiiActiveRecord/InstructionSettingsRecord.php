<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $public_key
 * @property bool $creation_locked
 */
final class InstructionSettingsRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_settings}}';
    }

    public function rules(): array
    {
        return [
            [['public_key'], 'required'],
            [['public_key'], 'integer'],
            [['creation_locked'], 'boolean'],
        ];
    }
}
