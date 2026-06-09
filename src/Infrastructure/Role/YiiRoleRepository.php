<?php

namespace app\Infrastructure\Role;

use app\Application\Role\Contract\RoleRepositoryInterface;
use app\Infrastructure\YiiActiveRecord\Roles;

final class YiiRoleRepository implements RoleRepositoryInterface
{
    public function findAllForClient(int $publicKey): array
    {
        return Roles::find()
            ->where(['public_key' => $publicKey])
            ->all();
    }

    public function newForClient(int $publicKey): Roles
    {
        $role = new Roles();
        $role->public_key = $publicKey;

        return $role;
    }

    public function findForClient(int $id, int $publicKey): ?Roles
    {
        return Roles::findOne(['id' => $id, 'public_key' => $publicKey]);
    }

    public function saveNewForClient(int $publicKey, array $post): bool
    {
        $role = $this->newForClient($publicKey);

        if (!$role->load($post)) {
            return false;
        }

        $role->public_key = $publicKey;

        return $role->save();
    }

    public function saveExisting(mixed $role, array $post): bool
    {
        return $role->load($post) && $role->save();
    }

    public function delete(mixed $role, array $post = []): bool
    {
        if ($post !== [] && !$role->load($post)) {
            return false;
        }

        return (bool)$role->delete();
    }
}
