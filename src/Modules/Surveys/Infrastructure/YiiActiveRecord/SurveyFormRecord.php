<?php

namespace app\Modules\Surveys\Infrastructure\YiiActiveRecord;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

final class SurveyFormRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sw_survey_forms';
    }

    public function rules(): array
    {
        return [
            [['public_key', 'title'], 'required'],
            [['public_key', 'question_count', 'sort_order'], 'integer'],
            [['is_active', 'is_important'], 'boolean'],
            [['date_start', 'date_finish', 'created_at', 'updated_at'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    public function getQuestions(): ActiveQuery
    {
        return $this->hasMany(SurveyQuestionRecord::class, ['survey_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
