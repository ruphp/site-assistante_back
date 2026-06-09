<?php

namespace app\Modules\Support\Application\UseCase;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportManagerRecipientRepositoryInterface;
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
        private readonly SupportManagerRecipientRepositoryInterface $managerRecipients,
    ) {
    }

    public function viewData(int $publicKey, string $defaultNotificationEmail = ''): SupportSettingsViewData
    {
        $settings = $this->settings->getForClient($publicKey);

        return new SupportSettingsViewData(
            $settings,
            SupportPlanLimit::forPlan($settings->plan),
            $this->managerRecipients->listForClient($publicKey),
            $defaultNotificationEmail,
        );
    }

    public function saveFromPost(int $publicKey, array $post): bool
    {
        if (!$this->moduleAccess->getForClient($publicKey)->allows(SupportModule::NAME)) {
            return false;
        }

        $data = $post['SupportSettings'] ?? [];
        $schedule = $this->schedule($post['SupportSchedule'] ?? []);

        return $this->settings->save(new SupportSettings(
            publicKey: $publicKey,
            plan: $this->settings->getForClient($publicKey)->plan,
            enabled: true,
            title: $this->text($data, 'title', 'Онлайн-поддержка'),
            welcomeMessage: $this->text($data, 'welcomeMessage', 'Здравствуйте! Напишите нам, мы поможем.'),
            offlineMessage: $this->text($data, 'offlineMessage', 'Мы сейчас не онлайн, но ответим позже.'),
            contactInfo: $this->text($data, 'contactInfo'),
            timezone: $this->text($data, 'timezone', 'Asia/Yekaterinburg'),
            workingHours: $this->workingHoursLabel($schedule['work'], $schedule['holidays']),
            workSchedule: $schedule['work'],
            holidaySchedule: $schedule['holidays'],
            askName: (bool)($data['askName'] ?? false),
            askEmail: (bool)($data['askEmail'] ?? false),
            askPhone: (bool)($data['askPhone'] ?? false),
            requireEmailOffline: (bool)($data['requireEmailOffline'] ?? false),
            autoReply: $this->text($data, 'autoReply', 'Спасибо, мы получили сообщение.'),
            pollingIntervalSeconds: max(3, min(60, (int)($data['pollingIntervalSeconds'] ?? 5))),
            notifyEmail: (bool)($data['notifyEmail'] ?? false),
            notificationEmails: $this->text($data, 'notificationEmails'),
            notifyTelegram: (bool)($data['notifyTelegram'] ?? false),
            telegramBotToken: $this->text($data, 'telegramBotToken'),
            telegramChatId: $this->text($data, 'telegramChatId'),
            notifyMax: (bool)($data['notifyMax'] ?? false),
            maxApiUrl: $this->text($data, 'maxApiUrl', 'https://platform-api.max.ru'),
            maxBotToken: $this->text($data, 'maxBotToken'),
            maxChatId: $this->text($data, 'maxChatId'),
        ));
    }

    private function text(array $data, string $key, string $default = ''): string
    {
        $value = trim((string)($data[$key] ?? $default));

        return $value !== '' ? $value : $default;
    }

    private function schedule(array $data): array
    {
        $mode = in_array(($data['mode'] ?? 'weekdays'), ['everyday', 'weekdays', 'custom'], true)
            ? (string)$data['mode']
            : 'weekdays';
        $roundTheClock = (bool)($data['roundTheClock'] ?? false);
        $rawDays = $data['days'] ?? [];
        $days = [];

        foreach ($this->weekDays() as $key => $label) {
            $day = is_array($rawDays[$key] ?? null) ? $rawDays[$key] : [];
            $enabled = (bool)($day['enabled'] ?? false);

            if ($mode === 'everyday') {
                $enabled = true;
            }
            if ($mode === 'weekdays') {
                $enabled = !in_array($key, ['sat', 'sun'], true);
            }

            $days[$key] = [
                'enabled' => $enabled,
                'from' => $this->time($day['from'] ?? '09:00', '09:00'),
                'to' => $this->time($day['to'] ?? '18:00', '18:00'),
            ];
        }

        return [
            'work' => [
                'mode' => $mode,
                'round_the_clock' => $roundTheClock,
                'days' => $days,
            ],
            'holidays' => $this->holidays($data['holidays'] ?? []),
        ];
    }

    private function holidays(array $rawHolidays): array
    {
        $holidays = [];

        foreach ($rawHolidays as $holiday) {
            if (!is_array($holiday)) {
                continue;
            }

            $date = trim((string)($holiday['date'] ?? ''));
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                continue;
            }

            $closed = (bool)($holiday['closed'] ?? false);
            $holidays[] = [
                'date' => $date,
                'closed' => $closed,
                'from' => $closed ? '' : $this->time($holiday['from'] ?? '09:00', '09:00'),
                'to' => $closed ? '' : $this->time($holiday['to'] ?? '18:00', '18:00'),
            ];
        }

        return $holidays;
    }

    private function workingHoursLabel(array $schedule, array $holidays): string
    {
        if (($schedule['round_the_clock'] ?? false) === true) {
            return 'Круглосуточно';
        }

        $days = $schedule['days'] ?? [];
        $enabled = array_filter($days, static fn(array $day): bool => (bool)($day['enabled'] ?? false));
        if ($enabled === []) {
            return 'Операторы офлайн';
        }

        $first = reset($enabled);
        $sameTime = true;
        foreach ($enabled as $day) {
            if (($day['from'] ?? '') !== ($first['from'] ?? '') || ($day['to'] ?? '') !== ($first['to'] ?? '')) {
                $sameTime = false;
                break;
            }
        }

        $label = match ($schedule['mode'] ?? 'custom') {
            'everyday' => 'Ежедневно',
            'weekdays' => 'Пн-Пт',
            default => 'По расписанию',
        };

        if ($sameTime) {
            $label .= ' ' . ($first['from'] ?? '09:00') . '-' . ($first['to'] ?? '18:00');
        }

        if ($holidays !== []) {
            $label .= ', есть исключения';
        }

        return $label;
    }

    private function time(mixed $value, string $default): string
    {
        $value = trim((string)$value);

        return preg_match('/^\d{2}:\d{2}$/', $value) ? $value : $default;
    }

    private function weekDays(): array
    {
        return [
            'mon' => 'Понедельник',
            'tue' => 'Вторник',
            'wed' => 'Среда',
            'thu' => 'Четверг',
            'fri' => 'Пятница',
            'sat' => 'Суббота',
            'sun' => 'Воскресенье',
        ];
    }
}
