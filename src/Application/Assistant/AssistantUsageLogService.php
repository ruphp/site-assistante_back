<?php

namespace app\Application\Assistant;

use app\Application\Assistant\Contract\AssistantEventLoggerInterface;
use app\Domain\Assistant\AssistantContext;

final class AssistantUsageLogService
{
    public function __construct(
        private readonly AssistantEventLoggerInterface $eventLogger,
    ) {
    }

    public function logOpen(AssistantContext $context): int
    {
        return $this->eventLogger->logOpen($context);
    }

    public function logUsage(AssistantContext $context, string $type): int
    {
        return $this->eventLogger->logUsage($context, $type);
    }
}
