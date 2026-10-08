<?php

namespace app\Modules\SmsDelivery\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SmsDeliveryTaskRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'sms_delivery_tasks';
    }
}
