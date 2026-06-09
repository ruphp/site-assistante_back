<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\AssistantConfigurationResponse;

interface BuildAssistantConfigurationUseCaseInterface
{
    public function build(BuildAssistantConfigurationRequest $request): AssistantConfigurationResponse;
}
