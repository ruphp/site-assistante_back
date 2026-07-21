<?php

namespace app\Modules\Support\Application\Reporting;

use app\Application\Panel\ClientProjectService;
use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Instructions\Domain\InstructionPlanLimit;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\SupportUsageOwnerReport;
use app\Modules\Support\Application\Dto\SupportUsageProjectReport;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportPlanLimit;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;

final class SupportUsageReportService
{
    public function __construct(
        private readonly ClientProjectService $projects,
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportUsageRepositoryInterface $usage,
    ) {
    }

    public function clientReport(int $ownerPublicKey): SupportUsageOwnerReport
    {
        $owner = Users::findOne(['id' => $ownerPublicKey, 'public_key' => $ownerPublicKey]);

        return $this->ownerReport(
            $ownerPublicKey,
            (int)($owner?->id ?? 0),
            $owner?->name ?? '',
            $owner?->email ?? '',
            $owner?->firm ?? '',
        );
    }

    public function adminReports(): array
    {
        $reports = [];
        $seenPublicKeys = [];

        foreach (Users::getListUsersManager() as $user) {
            $publicKey = (int)($user['public_key'] ?? 0);
            if ($publicKey <= 0 || isset($seenPublicKeys[$publicKey])) {
                continue;
            }
            if ((int)($user['id'] ?? 0) !== $publicKey) {
                continue;
            }
            if ($this->isChildProjectPublicKey($publicKey)) {
                continue;
            }
            $seenPublicKeys[$publicKey] = true;

            $reports[] = $this->ownerReport(
                $publicKey,
                (int)($user['id'] ?? 0),
                (string)($user['name'] ?? ''),
                (string)($user['email'] ?? ''),
                (string)($user['firm'] ?? ''),
            );
        }

        return $reports;
    }

    public function resetDailyOperatorRepliesForOwner(int $ownerPublicKey): void
    {
        $today = new \DateTimeImmutable('today');

        foreach ($this->projects->projectsForOwner($ownerPublicKey) as $project) {
            $this->usage->resetOperatorReplies($project->publicKey, $today);
        }
    }

    private function ownerReport(
        int $ownerPublicKey,
        int $ownerUserId,
        string $ownerName,
        string $ownerEmail,
        string $firm
    ): SupportUsageOwnerReport
    {
        $today = new \DateTimeImmutable('today');
        $month = new \DateTimeImmutable('first day of this month 00:00:00');
        $ownerSettings = $this->settings->getForClient($ownerPublicKey);
        $ownerPlan = SupportPlan::normalize($ownerSettings->plan);
        $ownerLimit = SupportPlanLimit::forPlan($ownerPlan);
        $instructionLimit = InstructionPlanLimit::forPlan($ownerPlan);

        $operatorRepliesToday = 0;
        $conversationsMonth = 0;
        $messagesMonth = 0;
        $projectReports = [];

        foreach ($this->projects->projectsForOwner($ownerPublicKey) as $project) {
            $projectOperatorRepliesToday = $this->usage->dailyOperatorReplyCount($project->publicKey, $today);
            $projectConversationsMonth = $this->usage->monthlyConversationCount($project->publicKey, $month);
            $projectMessagesMonth = $this->usage->monthlyMessageCount($project->publicKey, $month);

            $operatorRepliesToday += $projectOperatorRepliesToday;
            $conversationsMonth += $projectConversationsMonth;
            $messagesMonth += $projectMessagesMonth;

            $projectReports[] = new SupportUsageProjectReport(
                projectId: $project->id,
                projectName: $project->name,
                domain: $project->domain,
                publicKey: $project->publicKey,
                plan: $ownerPlan,
                planLabel: SupportPlan::labels()[$ownerPlan] ?? $ownerPlan,
                operatorRepliesToday: $projectOperatorRepliesToday,
                operatorRepliesPerDayLimit: $ownerLimit->maxOperatorRepliesPerDay,
                conversationsMonth: $projectConversationsMonth,
                conversationsMonthLimit: $ownerLimit->maxConversationsPerMonth,
                messagesMonth: $projectMessagesMonth,
                messagesMonthLimit: $ownerLimit->maxMessagesPerMonth,
                instructionStorageBytes: $this->instructionStorageBytes($project->publicKey),
                instructionStorageLimitBytes: $instructionLimit->storageBytes,
            );
        }

        return new SupportUsageOwnerReport(
            ownerPublicKey: $ownerPublicKey,
            ownerUserId: $ownerUserId,
            ownerName: $ownerName,
            ownerEmail: $ownerEmail,
            firm: $firm,
            plan: $ownerPlan,
            planLabel: SupportPlan::labels()[$ownerPlan] ?? $ownerPlan,
            operatorRepliesToday: $operatorRepliesToday,
            operatorRepliesPerDayLimit: $ownerLimit->maxOperatorRepliesPerDay,
            conversationsMonth: $conversationsMonth,
            conversationsMonthLimit: $ownerLimit->maxConversationsPerMonth,
            messagesMonth: $messagesMonth,
            messagesMonthLimit: $ownerLimit->maxMessagesPerMonth,
            projectsCount: count($projectReports),
            projectsLimit: $ownerLimit->maxProjects,
            operatorsCount: $this->operatorsCount($ownerPublicKey),
            operatorsLimit: $ownerLimit->maxOperators,
            projects: $projectReports,
        );
    }

    private function operatorsCount(int $publicKey): int
    {
        return (int)Users::find()
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where([
                'users.public_key' => $publicKey,
                'auth_assignment.item_name' => 'manager',
            ])
            ->count();
    }

    private function instructionStorageBytes(int $publicKey): int
    {
        return (int)InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey])
            ->sum('content_bytes');
    }

    private function isChildProjectPublicKey(int $publicKey): bool
    {
        return SupportProjectRecord::find()
            ->where(['public_key' => $publicKey])
            ->andWhere(['<>', 'owner_public_key', $publicKey])
            ->exists();
    }
}
