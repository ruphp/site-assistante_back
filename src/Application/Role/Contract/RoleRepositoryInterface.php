<?php

namespace app\Application\Role\Contract;

interface RoleRepositoryInterface
{
    /**
     * @return mixed[]
     */
    public function findAllForClient(int $publicKey): array;

    public function newForClient(int $publicKey): mixed;

    public function findForClient(int $id, int $publicKey): mixed;

    public function saveNewForClient(int $publicKey, array $post): bool;

    public function saveExisting(mixed $role, array $post): bool;

    public function delete(mixed $role, array $post = []): bool;
}
