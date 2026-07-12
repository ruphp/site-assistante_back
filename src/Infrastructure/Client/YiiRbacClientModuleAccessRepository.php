<?php

namespace app\Infrastructure\Client;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Domain\Client\ClientModuleAccess;
use app\Modules\Instructions\Domain\InstructionsModule;
use app\Modules\Support\Domain\SupportModule;
use Yii;

final class YiiRbacClientModuleAccessRepository implements ClientModuleAccessRepositoryInterface
{
    public function getForClient(int $publicKey): ClientModuleAccess
    {
        $auth = Yii::$app->authManager;
        $availableModules = array_keys($auth->getPermissionsByRole('accesses_modules'));
        $availableModules[] = InstructionsModule::NAME;
        $availableModules[] = SupportModule::NAME;
        $clientPermissions = array_keys($auth->getPermissionsByUser($publicKey));
        $clientPermissions[] = SupportModule::NAME;
        $clientPermissions[] = InstructionsModule::NAME;

        return new ClientModuleAccess(array_values(array_intersect($availableModules, $clientPermissions)));
    }
}
