<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $public_key
 * @property int $article_id
 * @property int $visitor_id
 * @property string $visitor_key
 */
final class InstructionArticleFavoriteRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_article_favorites}}';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'article_id', 'visitor_key'], 'required'],
            [['public_key', 'article_id', 'visitor_id'], 'integer'],
            [['visitor_key'], 'string', 'max' => 255],
        ];
    }
}
