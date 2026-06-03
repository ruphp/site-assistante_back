<?php

namespace app\Application\Role\Dto;

final class RoleOperationResult
{
    public const CREATED = 'created';
    public const UPDATED = 'updated';
    public const DELETED = 'deleted';
    public const FORBIDDEN = 'forbidden';
    public const FAILED = 'failed';
    public const NOOP = 'noop';

    public function __construct(
        public readonly string $status,
    ) {
    }

    public function isSuccessful(): bool
    {
        return in_array($this->status, [self::CREATED, self::UPDATED, self::DELETED], true);
    }
}
