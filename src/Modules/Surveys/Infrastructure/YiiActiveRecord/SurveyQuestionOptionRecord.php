<?php

namespace app\Modules\Surveys\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SurveyQuestionOptionRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sw_survey_question_options';
    }

    public function rules(): array
    {
        return [
            [['question_id', 'answer'], 'required'],
            [['question_id', 'sort_order'], 'integer'],
            [['answer'], 'string', 'max' => 500],
        ];
    }
}
