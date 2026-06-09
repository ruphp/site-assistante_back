<?php

namespace app\Infrastructure\Cron;

use app\Application\Cron\Contract\ActiveClientProviderInterface;
use app\Infrastructure\YiiActiveRecord\Users;

final class YiiActiveClientProvider implements ActiveClientProviderInterface
{
    public function getActiveClients(): array
    {
        return Users::find()->where(['status' => 1])->asArray()->all();
    }
}
