<?php

namespace app\Application\Admin;

use app\Application\Admin\Contract\ClientAccessRepositoryInterface;
use app\Application\Admin\Contract\ClientRepositoryInterface;
use app\Application\Admin\Dto\CreateClientRequest;
use app\Application\Admin\Dto\UpdateClientRequest;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlan;
use DomainException;

final class AdminClientService
{
    public function __construct(
        private readonly ClientRepositoryInterface $clients,
        private readonly ClientAccessRepositoryInterface $access,
        private readonly SupportSettingsRepositoryInterface $supportSettings,
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
        $client = $this->clients->findForAdminView($request->id);
        $publicKey = (int)($client?->public_key ?? $request->id);
        $this->supportSettings->save(
            $this->supportSettings->getForClient($publicKey)->withPlan(SupportPlan::normalize($request->supportPlan)),
        );

        return $newPassword;
    }

    public function getUpdateViewData(int $userId): array
    {
        $client = $this->clients->findForAdminView($userId);
        $publicKey = (int)($client?->public_key ?? $userId);

        return [
            'user' => $client,
            'moduleAccessView' => $this->access->getModuleAccessView($userId),
            'supportSettings' => $this->supportSettings->getForClient($publicKey),
            'supportPlanLabels' => SupportPlan::labels(),
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
