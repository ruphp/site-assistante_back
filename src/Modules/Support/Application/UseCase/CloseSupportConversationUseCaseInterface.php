<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Dto\CloseSupportConversationRequest;

interface CloseSupportConversationUseCaseInterface
{
    public function close(CloseSupportConversationRequest $request): void;
}
