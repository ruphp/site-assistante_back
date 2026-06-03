<?php

namespace app\Application\Assistant\Contract;

use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Domain\Assistant\AssistantContext;

interface AssistantConfigurationLoggerInterface
{
    public function log(BuildAssistantConfigurationRequest $request, AssistantContext $context): void;
}
