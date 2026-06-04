<?php

namespace app\Modules\Support\Application\UseCase;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportSettingsViewData;
use app\Modules\Support\Domain\SupportModule;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;

final class ManageSupportSettingsUseCase
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly ClientModuleAccessRepositoryInterface $moduleAccess,
    ) {
    }

    public function viewData(int $publicKey): SupportSettingsViewData
    {
        return new SupportSettingsViewData(
            $this->settings->getForClient($publicKey),
            SupportPlanLimit::free(),
        );
    }

    public function saveFromPost(int $publicKey, array $post): bool
    {
        if (!$this->moduleAccess->getForClient($publicKey)->allows(SupportModule::NAME)) {
            return false;
        }

        $data = $post['SupportSettings'] ?? [];

        return $this->settings->save(new SupportSettings(
            publicKey: $publicKey,
            enabled: (bool)($data['enabled'] ?? false),
            title: $this->text($data, 'title', 'Онлайн-поддержка'),
            welcomeMessage: $this->text($data, 'welcomeMessage', 'Здравствуйте! Напишите нам, мы поможем.'),
            offlineMessage: $this->text($data, 'offlineMessage', 'Мы сейчас не онлайн, но ответим позже.'),
            contactInfo: $this->text($data, 'contactInfo'),
            timezone: $this->text($data, 'timezone', 'Asia/Yekaterinburg'),
            workingHours: $this->text($data, 'workingHours', 'Пн-Пт 09:00-18:00'),
            askName: (bool)($data['askName'] ?? false),
            askEmail: (bool)($data['askEmail'] ?? false),
            askPhone: (bool)($data['askPhone'] ?? false),
            requireEmailOffline: (bool)($data['requireEmailOffline'] ?? false),
            autoReply: $this->text($data, 'autoReply', 'Спасибо, мы получили сообщение.'),
            pollingIntervalSeconds: max(3, min(60, (int)($data['pollingIntervalSeconds'] ?? 5))),
        ));
    }

    private function text(array $data, string $key, string $default = ''): string
    {
        $value = trim((string)($data[$key] ?? $default));

        return $value !== '' ? $value : $default;
    }
}
