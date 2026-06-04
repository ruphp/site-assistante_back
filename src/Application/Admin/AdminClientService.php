<?php

namespace app\Application\Admin;

use app\Application\Admin\Contract\ClientAccessRepositoryInterface;
use app\Application\Admin\Contract\ClientRepositoryInterface;
use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;
use DomainException;

final class AdminClientService
{
    public function __construct(
        private readonly ClientRepositoryInterface $clients,
        private readonly ClientAccessRepositoryInterface $access,
    ) {
    }

    public function listClients(): array
    {
        return $this->clients->listManagers();
    }

    public function createClient(CreateClientRequest $request): void
    {
        $userId = $this->clients->create($request);
        $this->access->assignManagerRole($userId);
        $this->access->syncModuleAccess($userId, ['support' => 1]);
    }

    public function deleteClient(int $userId): void
    {
        if (!$this->clients->deleteInactive($userId)) {
            throw new DomainException('Client was not found or is active');
        }
    }

    public function updateClient(UpdateClientRequest $request): ?string
    {
        $newPassword = null;

        if ($request->changePassword) {
            $newPassword = $this->generatePassword(8);
        }

        if (!$this->clients->update($request, $newPassword)) {
            throw new DomainException('Client was not found');
        }

        $this->access->syncModuleAccess($request->id, $request->modules);

        return $newPassword;
    }

    public function getUpdateViewData(int $userId): array
    {
        return [
            'user' => $this->clients->findForAdminView($userId),
            'moduleAccessView' => $this->access->getModuleAccessView($userId),
        ];
    }

    private function generatePassword(int $length): string
    {
        $chars = 'qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP';
        $max = strlen($chars) - 1;
        $password = '';

        while ($length-- > 0) {
            $password .= $chars[random_int(0, $max)];
        }

        return $password;
    }
}
