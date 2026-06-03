<?php

namespace app\Infrastructure\Monitoring;

use app\Application\Monitoring\Contract\ModuleContentCounterInterface;

final class YiiModuleContentCounter implements ModuleContentCounterInterface
{
    private array $modelByModule = [
        'courses' => 'app\modules\courses\models\Courses',
        'hints' => 'app\modules\hints\models\Hints',
        'onboardings' => 'app\modules\onboardings\models\Onboardings',
        'surveys' => 'app\modules\surveys\models\Surveys',
    ];

    public function count(string $moduleKey, int $publicKey): int
    {
        $modelClass = $this->modelByModule[$moduleKey] ?? null;

        if ($modelClass === null || !class_exists($modelClass)) {
            return 0;
        }

        return (int)$modelClass::find()
            ->where(['public_key' => $publicKey])
            ->count();
    }
}
