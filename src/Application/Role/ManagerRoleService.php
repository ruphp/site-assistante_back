<?php

namespace app\Application\Role;

use app\Application\Role\Contract\RoleRepositoryInterface;
use app\Application\Role\Dto\RoleOperationResult;
use app\Application\Role\Dto\RolePageData;

final class ManagerRoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roles,
    ) {
    }

    public function getPageData(int $publicKey): RolePageData
    {
        return new RolePageData(
            $this->roles->newForClient($publicKey),
            $this->roles->findAllForClient($publicKey),
        );
    }

    public function saveFromPost(int $publicKey, array $post): RoleOperationResult
    {
        $roleData = $post['Roles'] ?? null;

        if (!is_array($roleData)) {
            return new RoleOperationResult(RoleOperationResult::NOOP);
        }

        if (empty($roleData['id'])) {
            return new RoleOperationResult(
                $this->roles->saveNewForClient($publicKey, $post)
                    ? RoleOperationResult::CREATED
                    : RoleOperationResult::FAILED
            );
        }

        $role = $this->roles->findForClient((int)$roleData['id'], $publicKey);

        if ($role === null) {
            return new RoleOperationResult(RoleOperationResult::FORBIDDEN);
        }

        if (empty($roleData['name'])) {
            return new RoleOperationResult(
                $this->roles->delete($role, $post)
                    ? RoleOperationResult::DELETED
                    : RoleOperationResult::FAILED
            );
        }

        return new RoleOperationResult(
            $this->roles->saveExisting($role, $post)
                ? RoleOperationResult::UPDATED
                : RoleOperationResult::FAILED
        );
    }

    public function deleteById(int $id, int $publicKey): RoleOperationResult
    {
        $role = $this->roles->findForClient($id, $publicKey);

        if ($role === null) {
            return new RoleOperationResult(RoleOperationResult::FORBIDDEN);
        }

        return new RoleOperationResult(
            $this->roles->delete($role)
                ? RoleOperationResult::DELETED
                : RoleOperationResult::FAILED
        );
    }
}
