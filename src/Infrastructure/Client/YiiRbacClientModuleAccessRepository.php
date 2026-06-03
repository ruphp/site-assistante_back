<?php

namespace app\Infrastructure\Client;

use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Domain\Client\ClientModuleAccess;
use Yii;

final class YiiRbacClientModuleAccessRepository implements ClientModuleAccessRepositoryInterface
{
    public function getForClient(int $publicKey): ClientModuleAccess
    {
        $auth = Yii::$app->authManager;
        $availableModules = array_keys($auth->getPermissionsByRole('accesses_modules'));
        $clientPermissions = array_keys($auth->getPermissionsByUser($publicKey));

        return new ClientModuleAccess(array_values(array_intersect($availableModules, $clientPermissions)));
    }
}
