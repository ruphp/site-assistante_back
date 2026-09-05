<?php

namespace app\Modules\Support\Domain;

final class SupportSettings
{
    public function __construct(
        public readonly int $publicKey,
        public readonly string $plan = SupportPlan::FREE,
        public readonly ?string $planExpiresAt = null,
        public readonly ?string $trialStartedAt = null,
        public readonly bool $enabled = true,
        public readonly string $title = 'Онлайн-поддержка',
        public readonly string $welcomeMessage = 'Здравствуйте! Напишите нам, мы поможем.',
        public readonly string $offlineMessage = 'Сейчас операторы не в сети - но вы можете оставить сообщение, мы свяжемся с вами.',
        public readonly string $timezone = 'Asia/Yekaterinburg',
        public readonly string $workingHours = 'Пн-Пт 09:00-18:00',
        public readonly array $workSchedule = [],
        public readonly array $holidaySchedule = [],
        public readonly bool $keepWidgetOpenWhenOnline = true,
        public readonly int $autoOpenSnoozeMinutes = 0,
        public readonly bool $showBranding = true,
        public readonly bool $askName = true,
        public readonly bool $askEmail = true,
        public readonly bool $askPhone = true,
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

    public function withPlan(string $plan, ?string $planExpiresAt = null): self
    {
        $plan = SupportPlan::normalize($plan);

        return new self(
            publicKey: $this->publicKey,
            plan: $plan,
            planExpiresAt: $plan === SupportPlan::FREE ? null : $planExpiresAt,
            trialStartedAt: $this->trialStartedAt,
            enabled: $this->enabled,
            title: $this->title,
            welcomeMessage: $this->welcomeMessage,
            offlineMessage: $this->offlineMessage,
            timezone: $this->timezone,
            workingHours: $this->workingHours,
            workSchedule: $this->workSchedule,
            holidaySchedule: $this->holidaySchedule,
            keepWidgetOpenWhenOnline: $this->keepWidgetOpenWhenOnline,
            autoOpenSnoozeMinutes: $this->autoOpenSnoozeMinutes,
            showBranding: $this->showBranding,
            askName: $this->askName,
            askEmail: $this->askEmail,
            askPhone: $this->askPhone,
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

    public function hasExpired(?\DateTimeImmutable $now = null): bool
    {
        if ($this->plan === SupportPlan::FREE || $this->planExpiresAt === null) {
            return false;
        }

        $expiresAt = \DateTimeImmutable::createFromFormat('!Y-m-d H:i:s', $this->planExpiresAt)
            ?: new \DateTimeImmutable($this->planExpiresAt);

        return $expiresAt <= ($now ?? new \DateTimeImmutable());
    }

    public function effective(?\DateTimeImmutable $now = null): self
    {
        return $this->hasExpired($now)
            ? $this->withPlan(SupportPlan::FREE)
            : $this;
    }

    public function isTrialActive(?\DateTimeImmutable $now = null): bool
    {
        return $this->plan === SupportPlan::START
            && $this->trialStartedAt !== null
            && !$this->hasExpired($now);
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

