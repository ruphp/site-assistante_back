<?php

namespace app\Modules\Surveys\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SurveyResponseRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sw_survey_responses';
    }
}
