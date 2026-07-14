<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingHintRoleRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_hint_roles}}';
    }
}
