<?php

namespace app\Domain\Assistant;

final class AssistantContext
{
    public function __construct(
        public readonly AssistantClient $client,
        public readonly AssistantUserContext $user,
    ) {
    }
}
