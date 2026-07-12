<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $public_key
 * @property int|null $category_id
 * @property string $title
 * @property string $html
 * @property int $sort_order
 * @property bool $is_active
 */
final class InstructionArticleRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_articles}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'title', 'html'], 'required'],
            [['public_key', 'category_id', 'sort_order'], 'integer'],
            [['html'], 'string'],
            [['is_active'], 'boolean'],
            [['title'], 'string', 'max' => 255],
        ];
    }
}
