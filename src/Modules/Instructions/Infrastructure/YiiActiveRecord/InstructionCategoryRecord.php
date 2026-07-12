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
 * @property bool $admin_blocked
 * @property string|null $blocked_reason
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
            [['is_active', 'admin_blocked'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['blocked_reason'], 'string', 'max' => 500],
        ];
    }
}
