<?php

namespace app\Presentation\Console\Controller;

use app\Application\Cron\PrepareLogConfigurationService;
use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use yii\console\Controller;
use yii\console\ExitCode;
use Yii;

class CronController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly PrepareLogConfigurationService $prepareLogConfiguration,
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

    public function actionSupportAutoClose(): int
    {
        $repository = Yii::$container->get(SupportConversationRepositoryInterface::class);

        $seenTimeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_SEEN_MINUTES'] ?? 30);
        $seenClosed = $repository->closeExpiredAfterOperatorSeen(max(60, $seenTimeoutMinutes * 60));

        $replyTimeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_REPLY_MINUTES'] ?? 0);
        $replyClosed = $replyTimeoutMinutes > 0
            ? $repository->closeExpiredAfterOperatorReply(max(60, $replyTimeoutMinutes * 60))
            : 0;

        $this->stdout(sprintf(
            "Closed %d support conversations after visitor seen, %d after operator reply\n",
            $seenClosed,
            $replyClosed
        ));

        return ExitCode::OK;
    }
}
