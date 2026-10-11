<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Support\Application\Contract\SupportEntryPointRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Domain\SupportPlanLimit;

final class ManageSupportEntryPointsUseCase
{
    public function __construct(
        private readonly SupportEntryPointRepositoryInterface $entryPoints,
        private readonly SupportSettingsRepositoryInterface $settings,
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
        $data = is_array($post['SupportEntryPoint'] ?? null) ? $post['SupportEntryPoint'] : [];
        $id = (int)($data['id'] ?? 0);
        $isNew = $id <= 0;
        $limit = SupportPlanLimit::forPlan($this->settings->getForClient($publicKey)->plan);

        $entryPointCount = $this->entryPoints->countForClient($publicKey);

        if ($isNew && !$limit->canAddEntryPoint($entryPointCount)) {
            throw new \DomainException('На текущем тарифе достигнут лимит кнопок быстрых обращения');
        }

        $title = trim((string)($data['title'] ?? ''));
        if ($title === '') {
            throw new \InvalidArgumentException('Укажите название кнопки');
        }

        $description = trim((string)($data['description'] ?? ''));
        $responseType = SupportEntryPoint::normalizeResponseType((string)($data['responseType'] ?? SupportEntryPoint::RESPONSE_ANSWER));
        $instructionId = $responseType === SupportEntryPoint::RESPONSE_INSTRUCTION
            ? (int)($data['instructionId'] ?? 0)
            : null;
        if ($responseType === SupportEntryPoint::RESPONSE_INSTRUCTION && !$this->instructionExists($publicKey, $instructionId)) {
            throw new \InvalidArgumentException('Выберите доступную статью инструкции');
        }
        $rankLimit = min($limit->entryPointRankLimit(), max(1, $entryPointCount + ($isNew ? 1 : 0)));

        return $this->entryPoints->save(new SupportEntryPoint(
            id: $isNew ? null : $id,
            publicKey: $publicKey,
            title: mb_substr($title, 0, 255),
            description: mb_substr($description, 0, 2000),
            responseType: $responseType,
            priority: max(1, min($rankLimit, (int)($data['priority'] ?? 1))),
            enabled: (bool)($data['enabled'] ?? false),
            sortOrder: max(1, min($rankLimit, (int)($data['sortOrder'] ?? 1))),
            instructionId: $instructionId ?: null,
        ));
    }

    public function delete(int $publicKey, int $id): bool
    {
        return $this->entryPoints->deleteForClient($publicKey, $id);
    }

    private function instructionExists(int $publicKey, int $instructionId): bool
    {
        if ($instructionId <= 0) {
            return false;
        }

        return InstructionArticleRecord::find()
            ->where([
                'id' => $instructionId,
                'public_key' => $publicKey,
                'is_active' => true,
                'admin_blocked' => false,
            ])
            ->exists();
    }
}
