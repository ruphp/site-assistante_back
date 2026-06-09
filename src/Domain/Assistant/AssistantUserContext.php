<?php

namespace app\Domain\Assistant;

final class AssistantUserContext
{
    public function __construct(
        public readonly int $studentId,
        public readonly array $systemRoleIds,
        public readonly array $roleIds,
    ) {
    }
}
