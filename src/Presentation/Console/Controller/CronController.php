<?php

namespace app\Presentation\Console\Controller;

use app\Application\Cron\PrepareLogConfigurationService;
use app\Modules\Support\Infrastructure\YiiSupportConversationRepository;
use app\Modules\Support\Application\Contract\SupportPlanLifecycleRepositoryInterface;
use yii\console\Controller;
use yii\console\ExitCode;

class CronController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly PrepareLogConfigurationService $prepareLogConfiguration,
        private readonly YiiSupportConversationRepository $supportConversations,
        private readonly SupportPlanLifecycleRepositoryInterface $planLifecycle,
        $config = []
    ) {
        parent::__construct($id, $module, $config);
    }

    /**
     * @throws \Exception
     */
    public function actionPrepareLogConfiguration(): int
    {
        $this->prepareLogConfiguration->prepare();

        return ExitCode::OK;
    }

    public function actionTestCron(): int
    {
        echo "actionTestCron";

        return ExitCode::OK;
    }

    public function actionCloseExpiredSupportConversations(): int
    {
        $seenTimeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_SEEN_MINUTES'] ?? 30);
        $replyTimeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_REPLY_MINUTES'] ?? $seenTimeoutMinutes);

        $closedAfterSeen = $this->supportConversations->closeExpiredAfterOperatorSeen(max(60, $seenTimeoutMinutes * 60));
        $closedAfterReply = $this->supportConversations->closeExpiredAfterOperatorReply(max(60, $replyTimeoutMinutes * 60));
        $expiredPlans = $this->planLifecycle->expireElapsedPlans();

        echo sprintf(
            "Closed support conversations: after_seen=%d, after_reply=%d; expired_plans=%d\n",
            $closedAfterSeen,
            $closedAfterReply,
            $expiredPlans
        );

        return ExitCode::OK;
    }
}
