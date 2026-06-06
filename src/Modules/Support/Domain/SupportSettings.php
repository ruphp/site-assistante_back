<?php

namespace app\Modules\Support\Domain;

final class SupportSettings
{
    public function __construct(
        public readonly int $publicKey,
        public readonly string $plan = SupportPlan::FREE,
        public readonly bool $enabled = true,
        public readonly string $title = 'Онлайн-поддержка',
        public readonly string $welcomeMessage = 'Здравствуйте! Напишите нам, мы поможем.',
        public readonly string $offlineMessage = 'Мы сейчас не онлайн, но ответим позже.',
        public readonly string $contactInfo = '',
        public readonly string $timezone = 'Asia/Yekaterinburg',
        public readonly string $workingHours = 'Пн-Пт 09:00-18:00',
        public readonly array $workSchedule = [],
        public readonly array $holidaySchedule = [],
        public readonly bool $askName = false,
        public readonly bool $askEmail = true,
        public readonly bool $askPhone = false,
        public readonly bool $requireEmailOffline = true,
        public readonly string $autoReply = 'Спасибо, мы получили сообщение.',
        public readonly int $pollingIntervalSeconds = 5,
        public readonly bool $notifyEmail = true,
        public readonly string $notificationEmails = '',
        public readonly bool $notifyTelegram = false,
        public readonly string $telegramBotToken = '',
        public readonly string $telegramChatId = '',
        public readonly bool $notifyMax = false,
        public readonly string $maxApiUrl = 'https://platform-api.max.ru',
        public readonly string $maxBotToken = '',
        public readonly string $maxChatId = '',
    ) {
    }

    public function withPlan(string $plan): self
    {
        return new self(
            publicKey: $this->publicKey,
            plan: SupportPlan::normalize($plan),
            enabled: $this->enabled,
            title: $this->title,
            welcomeMessage: $this->welcomeMessage,
            offlineMessage: $this->offlineMessage,
            contactInfo: $this->contactInfo,
            timezone: $this->timezone,
            workingHours: $this->workingHours,
            workSchedule: $this->workSchedule,
            holidaySchedule: $this->holidaySchedule,
            askName: $this->askName,
            askEmail: $this->askEmail,
            askPhone: $this->askPhone,
            requireEmailOffline: $this->requireEmailOffline,
            autoReply: $this->autoReply,
            pollingIntervalSeconds: $this->pollingIntervalSeconds,
            notifyEmail: $this->notifyEmail,
            notificationEmails: $this->notificationEmails,
            notifyTelegram: $this->notifyTelegram,
            telegramBotToken: $this->telegramBotToken,
            telegramChatId: $this->telegramChatId,
            notifyMax: $this->notifyMax,
            maxApiUrl: $this->maxApiUrl,
            maxBotToken: $this->maxBotToken,
            maxChatId: $this->maxChatId,
        );
    }

    public function normalizedWorkSchedule(): array
    {
        if ($this->workSchedule !== []) {
            return $this->workSchedule;
        }

        return [
            'mode' => 'weekdays',
            'round_the_clock' => false,
            'days' => [
                'mon' => ['enabled' => true, 'from' => '09:00', 'to' => '18:00'],
                'tue' => ['enabled' => true, 'from' => '09:00', 'to' => '18:00'],
                'wed' => ['enabled' => true, 'from' => '09:00', 'to' => '18:00'],
                'thu' => ['enabled' => true, 'from' => '09:00', 'to' => '18:00'],
                'fri' => ['enabled' => true, 'from' => '09:00', 'to' => '18:00'],
                'sat' => ['enabled' => false, 'from' => '09:00', 'to' => '18:00'],
                'sun' => ['enabled' => false, 'from' => '09:00', 'to' => '18:00'],
            ],
        ];
    }
}
