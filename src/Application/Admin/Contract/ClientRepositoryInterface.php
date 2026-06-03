<?php

namespace app\Application\Admin\Contract;

use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;

interface ClientRepositoryInterface
{
    public function listManagers(): array;

    public function create(CreateClientRequest $request): int;

    public function deleteInactive(int $userId): bool;

    public function update(UpdateClientRequest $request, ?string $plainPassword): bool;

    public function findForAdminView(int $userId): mixed;
}
