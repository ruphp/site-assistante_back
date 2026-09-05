<?php

namespace app\Modules\Support\Application\Contract;

interface SupportPlanLifecycleRepositoryInterface
{
    public function startTrial(int $publicKey, int $days = 10): bool;

    public function expireElapsedPlans(): int;
}
