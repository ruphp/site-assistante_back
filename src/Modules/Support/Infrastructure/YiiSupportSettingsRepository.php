<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportSettingsRecord;

final class YiiSupportSettingsRepository implements SupportSettingsRepositoryInterface
{
    public function getForClient(int $publicKey): SupportSettings
    {
        $this->refreshSchema();
        $record = SupportSettingsRecord::findOne(['public_key' => $publicKey]);

        if ($record === null) {
            return new SupportSettings($publicKey);
        }

        return new SupportSettings(
            publicKey: (int)$record->public_key,
            plan: (string)$record->plan,
            enabled: (bool)$record->enabled,
            title: (string)$record->title,
            welcomeMessage: (string)$record->welcome_message,
            offlineMessage: (string)$record->offline_message,
            contactInfo: (string)$record->contact_info,
            timezone: (string)$record->timezone,
            workingHours: (string)$record->working_hours,
            workSchedule: $this->json($record->work_schedule, []),
            holidaySchedule: $this->json($record->holiday_schedule, []),
            askName: (bool)$record->ask_name,
            askEmail: (bool)$record->ask_email,
            askPhone: (bool)$record->ask_phone,
            requireEmailOffline: (bool)$record->require_email_offline,
            autoReply: (string)$record->auto_reply,
            pollingIntervalSeconds: (int)$record->polling_interval_seconds,
            notifyEmail: (bool)$record->notify_email,
            notificationEmails: (string)$record->notification_emails,
            notifyTelegram: (bool)$record->notify_telegram,
            telegramBotToken: (string)$record->telegram_bot_token,
            telegramChatId: (string)$record->telegram_chat_id,
            notifyMax: (bool)$record->notify_max,
            maxApiUrl: (string)$record->max_api_url,
            maxBotToken: (string)$record->max_bot_token,
            maxChatId: (string)$record->max_chat_id,
        );
    }

    public function save(SupportSettings $settings): bool
    {
        $this->refreshSchema();
        $record = SupportSettingsRecord::findOne(['public_key' => $settings->publicKey]) ?? new SupportSettingsRecord();
        $record->public_key = $settings->publicKey;
        $record->plan = $settings->plan;
        $record->enabled = $settings->enabled ? 1 : 0;
        $record->title = $settings->title;
        $record->welcome_message = $settings->welcomeMessage;
        $record->offline_message = $settings->offlineMessage;
        $record->contact_info = $settings->contactInfo;
        $record->timezone = $settings->timezone;
        $record->working_hours = $settings->workingHours;
        $record->work_schedule = $settings->normalizedWorkSchedule();
        $record->holiday_schedule = $settings->holidaySchedule;
        $record->ask_name = $settings->askName ? 1 : 0;
        $record->ask_email = $settings->askEmail ? 1 : 0;
        $record->ask_phone = $settings->askPhone ? 1 : 0;
        $record->require_email_offline = $settings->requireEmailOffline ? 1 : 0;
        $record->auto_reply = $settings->autoReply;
        $record->polling_interval_seconds = $settings->pollingIntervalSeconds;
        $record->notify_email = $settings->notifyEmail ? 1 : 0;
        $record->notification_emails = $settings->notificationEmails;
        $record->notify_telegram = $settings->notifyTelegram ? 1 : 0;
        $record->telegram_bot_token = $settings->telegramBotToken;
        $record->telegram_chat_id = $settings->telegramChatId;
        $record->notify_max = $settings->notifyMax ? 1 : 0;
        $record->max_api_url = $settings->maxApiUrl;
        $record->max_bot_token = $settings->maxBotToken;
        $record->max_chat_id = $settings->maxChatId;

        return $record->save(false);
    }

    private function json(mixed $value, array $default): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : $default;
        }

        return $default;
    }

    private function refreshSchema(): void
    {
        SupportSettingsRecord::getDb()->schema->refreshTableSchema(SupportSettingsRecord::tableName());
    }
}
