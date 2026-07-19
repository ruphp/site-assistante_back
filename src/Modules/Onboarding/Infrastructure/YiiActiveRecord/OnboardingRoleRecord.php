<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingRoleRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_roles}}';
    }
}
