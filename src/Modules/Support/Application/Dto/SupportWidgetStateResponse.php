<?php

namespace app\Modules\Support\Application\Dto;

use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Domain\SupportSettings;

final class SupportWidgetStateResponse
{
    public function __construct(
        public readonly SupportSettings $settings,
        public readonly SupportPlanLimit $limit,
        public readonly int $usedConversations,
        public readonly int $usedMessages,
        public readonly array $entryPoints = [],
    ) {
    }

    public function toArray(): array
    {
        return [
            'enabled' => $this->settings->enabled,
            'plan' => $this->settings->plan,
            'title' => $this->settings->title,
            'welcome_message' => $this->settings->welcomeMessage,
            'offline_message' => $this->settings->offlineMessage,
            'contact_info' => $this->settings->contactInfo,
            'timezone' => $this->settings->timezone,
            'working_hours' => $this->settings->workingHours,
            'work_schedule' => $this->settings->normalizedWorkSchedule(),
            'holiday_schedule' => $this->settings->holidaySchedule,
            'visitor_form' => [
                'ask_name' => $this->settings->askName,
                'ask_email' => $this->settings->askEmail,
                'ask_phone' => $this->settings->askPhone,
                'require_email_offline' => $this->settings->requireEmailOffline,
            ],
            'auto_reply' => $this->settings->autoReply,
            'polling_interval_seconds' => $this->settings->pollingIntervalSeconds,
            'limits' => [
                'operators' => $this->limit->maxOperators,
                'conversations_per_month' => $this->limit->maxConversationsPerMonth,
                'messages_per_month' => $this->limit->maxMessagesPerMonth,
                'history_days' => $this->limit->historyDays,
                'attachments_enabled' => $this->limit->attachmentsEnabled,
                'entry_points' => $this->limit->maxEntryPoints,
                'entry_point_priority' => $this->limit->entryPointRankLimit(),
                'used_conversations' => $this->usedConversations,
                'used_messages' => $this->usedMessages,
            ],
            'entry_points' => array_map(
                static fn($entryPoint): array => (new SupportEntryPointResponse($entryPoint))->toArray(),
                $this->entryPoints,
            ),
        ];
    }
}
