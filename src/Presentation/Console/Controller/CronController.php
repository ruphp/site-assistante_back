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
        $timeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_SEEN_MINUTES'] ?? 30);
        $timeoutSeconds = max(60, $timeoutMinutes * 60);

        $repository = Yii::$container->get(SupportConversationRepositoryInterface::class);
        $closed = $repository->closeExpiredAfterOperatorSeen($timeoutSeconds);

        $this->stdout(sprintf("Closed %d support conversations\n", $closed));

        return ExitCode::OK;
    }
}
