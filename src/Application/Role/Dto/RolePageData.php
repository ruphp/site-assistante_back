<?php

namespace app\Application\Role\Dto;

final class RolePageData
{
    /**
     * @param mixed[] $roles
     */
    public function __construct(
        public readonly mixed $newrole,
        public readonly array $roles,
    ) {
    }

    public function toArray(): array
    {
        return [
            'newrole' => $this->newrole,
            'roles' => $this->roles,
        ];
    }
}
