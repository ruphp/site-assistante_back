<?php

namespace app\Application\Assistant\Dto;

final class BuildAssistantConfigurationRequest
{
    public function __construct(
        public readonly int $publicKey,
        public readonly ?AssistantRequestContext $requestContext = null,
    ) {
    }
}
