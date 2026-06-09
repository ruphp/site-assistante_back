<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\Dto\LogAssistantOpenRequest;

interface LogAssistantOpenUseCaseInterface
{
    public function log(LogAssistantOpenRequest $request): int;
}
