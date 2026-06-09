<?php

namespace app\Modules\Support\Application\UseCase;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportEntryPointRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Domain\SupportModule;
use app\Modules\Support\Domain\SupportPlanLimit;

final class ManageSupportEntryPointsUseCase
{
    public function __construct(
        private readonly SupportEntryPointRepositoryInterface $entryPoints,
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly ClientModuleAccessRepositoryInterface $moduleAccess,
    ) {
    }

    public function viewData(int $publicKey): array
    {
        $settings = $this->settings->getForClient($publicKey);

        return [
            'entryPoints' => $this->entryPoints->listForClient($publicKey),
            'limit' => SupportPlanLimit::forPlan($settings->plan),
            'plan' => $settings->plan,
        ];
    }

    public function saveFromPost(int $publicKey, array $post): bool
    {
        if (!$this->moduleAccess->getForClient($publicKey)->allows(SupportModule::NAME)) {
            return false;
        }

        $data = is_array($post['SupportEntryPoint'] ?? null) ? $post['SupportEntryPoint'] : [];
        $id = (int)($data['id'] ?? 0);
        $isNew = $id <= 0;
        $limit = SupportPlanLimit::forPlan($this->settings->getForClient($publicKey)->plan);

        $entryPointCount = $this->entryPoints->countForClient($publicKey);

        if ($isNew && !$limit->canAddEntryPoint($entryPointCount)) {
            throw new \DomainException('На Free-тарифе можно создать только одну кнопку обращения');
        }

        $title = trim((string)($data['title'] ?? ''));
        if ($title === '') {
            throw new \InvalidArgumentException('Укажите название кнопки');
        }

        $rankLimit = min($limit->entryPointRankLimit(), max(1, $entryPointCount + ($isNew ? 1 : 0)));

        return $this->entryPoints->save(new SupportEntryPoint(
            id: $isNew ? null : $id,
            publicKey: $publicKey,
            title: mb_substr($title, 0, 255),
            description: '',
            priority: max(1, min($rankLimit, (int)($data['priority'] ?? 1))),
            enabled: (bool)($data['enabled'] ?? false),
            sortOrder: max(1, min($rankLimit, (int)($data['sortOrder'] ?? 1))),
        ));
    }

    public function delete(int $publicKey, int $id): bool
    {
        if (!$this->moduleAccess->getForClient($publicKey)->allows(SupportModule::NAME)) {
            return false;
        }

        return $this->entryPoints->deleteForClient($publicKey, $id);
    }
}
