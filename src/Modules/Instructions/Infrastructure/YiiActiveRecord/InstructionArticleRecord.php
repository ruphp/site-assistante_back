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
 * @property bool $admin_blocked
 * @property string|null $blocked_reason
 * @property int $views
 * @property int $likes
 * @property int $dislikes
 * @property int $content_bytes
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
            [['public_key', 'category_id', 'sort_order', 'views', 'likes', 'dislikes', 'content_bytes'], 'integer'],
            [['html'], 'string'],
            [['is_active', 'admin_blocked'], 'boolean'],
            [['title'], 'string', 'max' => 255],
            [['blocked_reason'], 'string', 'max' => 500],
        ];
    }

    public function beforeSave($insert): bool
    {
        $this->content_bytes = strlen((string)$this->html);

        return parent::beforeSave($insert);
    }
}
