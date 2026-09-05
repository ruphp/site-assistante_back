<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPlanLifecycleRepositoryInterface;
use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportSettingsRecord;
use yii\db\Expression;

final class YiiSupportSettingsRepository implements SupportSettingsRepositoryInterface, SupportPlanLifecycleRepositoryInterface
{
    public function getForClient(int $publicKey): SupportSettings
    {
        $this->refreshSchema();
        $record = SupportSettingsRecord::findOne(['public_key' => $publicKey]);

        if ($record === null) {
            return new SupportSettings($publicKey);
        }

        return (new SupportSettings(
            publicKey: (int)$record->public_key,
            plan: (string)$record->plan,
            planExpiresAt: $this->date($record->plan_expires_at),
            trialStartedAt: $this->date($record->trial_started_at),
            enabled: (bool)$record->enabled,
            title: (string)$record->title,
            welcomeMessage: (string)$record->welcome_message,
            offlineMessage: (string)$record->offline_message,
            timezone: (string)$record->timezone,
            workingHours: (string)$record->working_hours,
            workSchedule: $this->json($record->work_schedule, []),
            holidaySchedule: $this->json($record->holiday_schedule, []),
            keepWidgetOpenWhenOnline: (bool)$record->keep_widget_open_when_online,
            autoOpenSnoozeMinutes: max(0, (int)$record->auto_open_snooze_minutes),
            showBranding: $record->hasAttribute('show_branding') ? (bool)$record->show_branding : true,
            askName: (bool)$record->ask_name,
            askEmail: (bool)$record->ask_email,
            askPhone: (bool)$record->ask_phone,
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
        ))->effective();
    }

    public function save(SupportSettings $settings): bool
    {
        $this->refreshSchema();
        $record = SupportSettingsRecord::findOne(['public_key' => $settings->publicKey]) ?? new SupportSettingsRecord();
        $record->public_key = $settings->publicKey;
        $record->plan = $settings->plan;
        $record->plan_expires_at = $settings->planExpiresAt;
        $record->trial_started_at = $settings->trialStartedAt;
        $record->enabled = $settings->enabled ? 1 : 0;
        $record->title = $settings->title;
        $record->welcome_message = $settings->welcomeMessage;
        $record->offline_message = $settings->offlineMessage;
        $record->timezone = $settings->timezone;
        $record->working_hours = $settings->workingHours;
        $record->work_schedule = $settings->normalizedWorkSchedule();
        $record->holiday_schedule = $settings->holidaySchedule;
        $record->keep_widget_open_when_online = $settings->keepWidgetOpenWhenOnline ? 1 : 0;
        $record->auto_open_snooze_minutes = max(0, $settings->autoOpenSnoozeMinutes);
        if ($record->hasAttribute('show_branding')) {
            $record->show_branding = $settings->showBranding ? 1 : 0;
        }
        $record->ask_name = $settings->askName ? 1 : 0;
        $record->ask_email = $settings->askEmail ? 1 : 0;
        $record->ask_phone = $settings->askPhone ? 1 : 0;
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

    public function startTrial(int $publicKey, int $days = 10): bool
    {
        $this->refreshSchema();
        $record = SupportSettingsRecord::findOne(['public_key' => $publicKey]);
        if ($record === null) {
            if (!$this->save(new SupportSettings($publicKey))) {
                return false;
            }
            $record = SupportSettingsRecord::findOne(['public_key' => $publicKey]);
        }
        if ($record === null || $record->trial_started_at !== null) {
            return $record !== null;
        }
        if (SupportPlan::normalize((string)$record->plan) !== SupportPlan::FREE) {
            return true;
        }

        $days = max(1, $days);
        $record->plan = SupportPlan::START;
        $record->trial_started_at = new Expression('CURRENT_TIMESTAMP');
        $record->plan_expires_at = new Expression("CURRENT_TIMESTAMP + INTERVAL '{$days} days'");

        return $record->save(false);
    }

    public function expireElapsedPlans(): int
    {
        $this->refreshSchema();

        return SupportSettingsRecord::updateAll(
            [
                'plan' => SupportPlan::FREE,
                'plan_expires_at' => null,
            ],
            [
                'and',
                ['<>', 'plan', SupportPlan::FREE],
                ['not', ['plan_expires_at' => null]],
                ['<=', 'plan_expires_at', new Expression('CURRENT_TIMESTAMP')],
            ]
        );
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

    private function date(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return $value instanceof \DateTimeInterface
            ? $value->format('Y-m-d H:i:s')
            : (string)$value;
    }

    private function refreshSchema(): void
    {
        SupportSettingsRecord::getDb()->schema->refreshTableSchema(SupportSettingsRecord::tableName());
    }
}

