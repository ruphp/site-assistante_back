<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $article_id
 * @property int $public_key
 * @property string $url
 * @property bool $include_children
 * @property bool $include_query
 */
final class InstructionArticleUrlRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_article_urls}}';
    }

    public function rules(): array
    {
        return [
            [['article_id', 'public_key', 'url'], 'required'],
            [['article_id', 'public_key'], 'integer'],
            [['include_children', 'include_query'], 'boolean'],
            [['url'], 'string', 'max' => 500],
        ];
    }
}
