<?php

namespace tests\Integration\Infrastructure\Admin;

use app\Infrastructure\Admin\YiiClientAccessRepository;
use tests\Integration\Support\YiiIntegrationTestCase;
use Yii;

final class YiiClientAccessRepositoryTest extends YiiIntegrationTestCase
{
    public function testIgnoresPermissionOutsideModuleAccessGroup(): void
    {
        $clientId = 990401;
        $this->createClient($clientId);

        $auth = Yii::$app->authManager;
        $permissionName = 'test_non_module_permission_' . $clientId;
        $permission = $auth->createPermission($permissionName);
        $permission->description = 'Not a module permission';
        $auth->add($permission);

        (new YiiClientAccessRepository())->syncModuleAccess($clientId, [
            $permissionName => 1,
        ]);

        self::assertArrayNotHasKey($permissionName, $auth->getPermissionsByUser($clientId));
    }

    public function testDisablesBigdataWhenChatbotsIsNotGranted(): void
    {
        $clientId = 990402;
        $this->createClient($clientId);

        (new YiiClientAccessRepository())->syncModuleAccess($clientId, [
            'chatbots' => 0,
            'bigdata' => 1,
        ]);

        $permissions = Yii::$app->authManager->getPermissionsByUser($clientId);

        self::assertArrayNotHasKey('bigdata', $permissions);
    }
}
