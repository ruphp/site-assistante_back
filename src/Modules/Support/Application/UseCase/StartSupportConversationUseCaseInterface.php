<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Dto\StartSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportConversationResponse;

interface StartSupportConversationUseCaseInterface
{
    public function start(StartSupportConversationRequest $request): SupportConversationResponse;
}
