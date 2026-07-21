<?php

namespace app\Modules\Surveys\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SurveyRoleRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sw_survey_roles';
    }
}
