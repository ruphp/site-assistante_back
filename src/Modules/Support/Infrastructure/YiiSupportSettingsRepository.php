<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportSettingsRecord;

final class YiiSupportSettingsRepository implements SupportSettingsRepositoryInterface
{
    public function getForClient(int $publicKey): SupportSettings
    {
        $record = SupportSettingsRecord::findOne(['public_key' => $publicKey]);

        if ($record === null) {
            return new SupportSettings($publicKey);
        }

        return new SupportSettings(
            publicKey: (int)$record->public_key,
            enabled: (bool)$record->enabled,
            title: (string)$record->title,
            welcomeMessage: (string)$record->welcome_message,
            offlineMessage: (string)$record->offline_message,
            contactInfo: (string)$record->contact_info,
            timezone: (string)$record->timezone,
            workingHours: (string)$record->working_hours,
            askName: (bool)$record->ask_name,
            askEmail: (bool)$record->ask_email,
            askPhone: (bool)$record->ask_phone,
            requireEmailOffline: (bool)$record->require_email_offline,
            autoReply: (string)$record->auto_reply,
            pollingIntervalSeconds: (int)$record->polling_interval_seconds,
        );
    }

    public function save(SupportSettings $settings): bool
    {
        $record = SupportSettingsRecord::findOne(['public_key' => $settings->publicKey]) ?? new SupportSettingsRecord();
        $record->public_key = $settings->publicKey;
        $record->enabled = $settings->enabled ? 1 : 0;
        $record->title = $settings->title;
        $record->welcome_message = $settings->welcomeMessage;
        $record->offline_message = $settings->offlineMessage;
        $record->contact_info = $settings->contactInfo;
        $record->timezone = $settings->timezone;
        $record->working_hours = $settings->workingHours;
        $record->ask_name = $settings->askName ? 1 : 0;
        $record->ask_email = $settings->askEmail ? 1 : 0;
        $record->ask_phone = $settings->askPhone ? 1 : 0;
        $record->require_email_offline = $settings->requireEmailOffline ? 1 : 0;
        $record->auto_reply = $settings->autoReply;
        $record->polling_interval_seconds = $settings->pollingIntervalSeconds;

        return $record->save(false);
    }
}
