<?php

namespace app\Presentation\Console\Controller;

use app\Application\Cron\PrepareLogConfigurationService;
use yii\console\Controller;
use yii\console\ExitCode;

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
}
