<?php

namespace app\Infrastructure\Cron;

use app\Application\Cron\Contract\ClientModuleCronRunnerInterface;
use tebazil\runner\ConsoleCommandRunner;
use Yii;

final class YiiClientModuleCronRunner implements ClientModuleCronRunnerInterface
{
    public function runForClient(array $client): void
    {
        $auth = Yii::$app->authManager;
        $runner = new ConsoleCommandRunner();
        $publicKey = (int)$client['public_key'];
        $modulePermissions = $auth->getChildren('accesses_modules');
        $clientPermissions = $auth->getPermissionsByUser($publicKey);

        foreach ($clientPermissions as $moduleName => $permission) {
            if (!isset($modulePermissions[$moduleName])) {
                continue;
            }

            $module = Yii::$app->getModule($moduleName);

            if ($module === null || empty($module->params['crons']) || !is_array($module->params['crons'])) {
                continue;
            }

            foreach ($module->params['crons'] as $cron) {
                $runner->run($cron, [$client]);
            }
        }
    }
}
