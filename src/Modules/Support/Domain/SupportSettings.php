<?php

namespace app\Modules\Support\Domain;

final class SupportSettings
{
    public function __construct(
        public readonly int $publicKey,
        public readonly bool $enabled = true,
        public readonly string $title = 'Онлайн-поддержка',
        public readonly string $welcomeMessage = 'Здравствуйте! Напишите нам, мы поможем.',
        public readonly string $offlineMessage = 'Мы сейчас не онлайн, но ответим позже.',
        public readonly string $contactInfo = '',
        public readonly string $timezone = 'Asia/Yekaterinburg',
        public readonly string $workingHours = 'Пн-Пт 09:00-18:00',
        public readonly bool $askName = false,
        public readonly bool $askEmail = true,
        public readonly bool $askPhone = false,
        public readonly bool $requireEmailOffline = true,
        public readonly string $autoReply = 'Спасибо, мы получили сообщение.',
        public readonly int $pollingIntervalSeconds = 5,
    ) {
    }
}
