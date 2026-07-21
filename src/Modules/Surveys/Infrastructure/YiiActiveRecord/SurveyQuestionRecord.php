<?php

namespace app\Modules\Surveys\Infrastructure\YiiActiveRecord;

use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

final class SurveyQuestionRecord extends ActiveRecord
{
    public const TYPE_TEXT = 'text';
    public const TYPE_RADIO = 'radiobutton';
    public const TYPE_CHECKBOX = 'checkbox';
    public const TYPE_RATE = 'rate';

    public static function tableName(): string
    {
        return 'sw_survey_questions';
    }

    public function rules(): array
    {
        return [
            [['survey_id', 'title'], 'required'],
            [['survey_id', 'sort_order'], 'integer'],
            [['title'], 'string'],
            [['is_required', 'is_free_answer'], 'boolean'],
            [['type'], 'in', 'range' => [self::TYPE_TEXT, self::TYPE_RADIO, self::TYPE_CHECKBOX, self::TYPE_RATE]],
        ];
    }

    public function getOptions(): ActiveQuery
    {
        return $this->hasMany(SurveyQuestionOptionRecord::class, ['question_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);
    }
}
