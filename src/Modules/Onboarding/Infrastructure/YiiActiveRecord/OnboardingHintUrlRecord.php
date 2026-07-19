<?php

namespace app\Modules\Onboarding\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class OnboardingHintUrlRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return '{{%sw_onboarding_hint_urls}}';
    }
}
