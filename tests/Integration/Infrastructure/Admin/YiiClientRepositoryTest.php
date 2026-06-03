<?php

namespace tests\Integration\Infrastructure\Admin;

use app\Application\Admin\Dto\UpdateClientRequest;
use app\Infrastructure\Admin\YiiClientRepository;
use app\Infrastructure\YiiActiveRecord\Users;
use tests\Integration\Support\YiiIntegrationTestCase;

final class YiiClientRepositoryTest extends YiiIntegrationTestCase
{
    public function testUpdatesOnlyManagerClients(): void
    {
        $clientId = 990101;
        $adminId = 990102;

        $this->createClient($clientId);
        $this->assignRole($clientId, 'manager');

        $this->createClient($adminId);
        $this->assignRole($adminId, 'admin');

        $repository = new YiiClientRepository();

        self::assertTrue($repository->update(new UpdateClientRequest(
            $clientId,
            'Updated client',
            'updated-client',
            'updated-client@help-layer.local',
            1,
            5,
            false,
            [],
        ), null));

        self::assertFalse($repository->update(new UpdateClientRequest(
            $adminId,
            'Hacked admin',
            'hacked-admin',
            'hacked-admin@help-layer.local',
            1,
            5,
            false,
            [],
        ), null));

        self::assertSame('Updated client', Users::findOne($clientId)->firm);
        self::assertNotSame('Hacked admin', Users::findOne($adminId)->firm);
    }

    public function testDeletesOnlyInactiveManagerClients(): void
    {
        $inactiveClientId = 990103;
        $activeClientId = 990104;
        $adminId = 990105;

        $this->createClient($inactiveClientId);
        $this->assignRole($inactiveClientId, 'manager');
        Users::updateAll(['status' => 0], ['id' => $inactiveClientId]);

        $this->createClient($activeClientId);
        $this->assignRole($activeClientId, 'manager');

        $this->createClient($adminId);
        $this->assignRole($adminId, 'admin');
        Users::updateAll(['status' => 0], ['id' => $adminId]);

        $repository = new YiiClientRepository();

        self::assertTrue($repository->deleteInactive($inactiveClientId));
        self::assertFalse($repository->deleteInactive($activeClientId));
        self::assertFalse($repository->deleteInactive($adminId));

        self::assertNull(Users::findOne($inactiveClientId));
        self::assertNotNull(Users::findOne($activeClientId));
        self::assertNotNull(Users::findOne($adminId));
    }
}
