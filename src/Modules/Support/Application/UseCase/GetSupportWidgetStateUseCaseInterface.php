<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Dto\GetSupportWidgetStateRequest;
use app\Modules\Support\Application\Dto\SupportWidgetStateResponse;

interface GetSupportWidgetStateUseCaseInterface
{
    public function get(GetSupportWidgetStateRequest $request): SupportWidgetStateResponse;
}
