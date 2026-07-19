<?php

namespace app\Modules\Instructions\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

/**
 * @property int $id
 * @property int $article_id
 * @property int $role_id
 */
final class InstructionArticleRoleRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_instruction_article_roles}}';
    }

    public function rules(): array
    {
        return [
            [['article_id', 'role_id'], 'required'],
            [['article_id', 'role_id'], 'integer'],
        ];
    }
}
