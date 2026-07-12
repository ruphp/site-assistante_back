<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $public_key
 * @property int|null $parent_id
 * @property string $name
 * @property int $sort_order
 * @property bool $is_active
 */
final class InstructionCategoryRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_categories}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'name'], 'required'],
            [['public_key', 'parent_id', 'sort_order'], 'integer'],
            [['is_active'], 'boolean'],
            [['name'], 'string', 'max' => 255],
        ];
    }
}
