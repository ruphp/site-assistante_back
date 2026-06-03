<?php

namespace tests\Integration\Application\Admin;

use app\Application\Admin\AdminClientService;
use app\Application\Admin\Dto\UpdateClientRequest;
use app\Infrastructure\Admin\YiiClientAccessRepository;
use app\Infrastructure\Admin\YiiClientRepository;
use DomainException;
use tests\Integration\Support\YiiIntegrationTestCase;
use Yii;

final class AdminClientServiceTest extends YiiIntegrationTestCase
{
    public function testDoesNotSyncModuleAccessWhenUserIsNotManagerClient(): void
    {
        $adminId = 990301;

        $this->createClient($adminId);
        $this->assignRole($adminId, 'admin');

        $service = new AdminClientService(
            new YiiClientRepository(),
            new YiiClientAccessRepository(),
        );

        try {
            $service->updateClient(new UpdateClientRequest(
                $adminId,
                'Hacked admin',
                'hacked-admin',
                'hacked-admin@help-layer.local',
                1,
                5,
                false,
                ['chatbots' => 1],
            ));
        } catch (DomainException) {
        }

        $permissions = Yii::$app->authManager->getPermissionsByUser($adminId);

        self::assertArrayNotHasKey('chatbots', $permissions);
    }
}
