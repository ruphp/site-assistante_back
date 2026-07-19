<?php

namespace app\Modules\Support\Infrastructure\YiiActiveRecord;

use yii\db\ActiveRecord;

final class SupportPushDeviceRecord extends ActiveRecord
{
    public static function tableName(): string
    {
        return 'support_push_devices';
    }
}
