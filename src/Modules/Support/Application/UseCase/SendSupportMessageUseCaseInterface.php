<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Dto\SendSupportMessageRequest;
use app\Modules\Support\Application\Dto\SupportMessageResponse;

interface SendSupportMessageUseCaseInterface
{
    public function send(SendSupportMessageRequest $request): SupportMessageResponse;
}
