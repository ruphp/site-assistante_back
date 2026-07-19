<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $public_key
 * @property int $article_id
 * @property int $visitor_id
 * @property string $visitor_key
 * @property bool $is_like
 * @property string|null $comment
 */
final class InstructionArticleFeedbackRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_article_feedback}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'article_id', 'visitor_key', 'is_like'], 'required'],
            [['public_key', 'article_id', 'visitor_id'], 'integer'],
            [['is_like'], 'boolean'],
            [['comment'], 'string'],
            [['visitor_key'], 'string', 'max' => 255],
        ];
    }
}
