<?php

namespace app\Application\Assistant\Contract;

use app\Domain\Assistant\AssistantContext;

interface AssistantEventLoggerInterface
{
    public function logOpen(AssistantContext $context): int;

    public function logUsage(AssistantContext $context, string $type): int;
}
