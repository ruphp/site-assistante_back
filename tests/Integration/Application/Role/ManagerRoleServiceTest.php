<?php

namespace tests\Integration\Application\Role;

use app\Application\Role\Dto\RoleOperationResult;
use app\Application\Role\ManagerRoleService;
use app\Infrastructure\Role\YiiRoleRepository;
use app\Infrastructure\YiiActiveRecord\Roles;
use tests\Integration\Support\YiiIntegrationTestCase;

final class ManagerRoleServiceTest extends YiiIntegrationTestCase
{
    public function testCreateRoleIgnoresForeignPublicKeyFromPost(): void
    {
        $currentPublicKey = 990301;
        $foreignPublicKey = 990302;

        $this->createClient($currentPublicKey);
        $this->createClient($foreignPublicKey);

        $service = new ManagerRoleService(new YiiRoleRepository());
        $result = $service->saveFromPost($currentPublicKey, [
            'Roles' => [
                'public_key' => $foreignPublicKey,
                'name' => 'Editor',
                'id_role_in_system' => 101,
            ],
        ]);

        self::assertSame(RoleOperationResult::CREATED, $result->status);
        self::assertNotNull(Roles::findOne(['public_key' => $currentPublicKey, 'id_role_in_system' => 101]));
        self::assertNull(Roles::findOne(['public_key' => $foreignPublicKey, 'id_role_in_system' => 101]));
    }

    public function testUpdateForeignRoleIsForbidden(): void
    {
        $currentPublicKey = 990303;
        $foreignPublicKey = 990304;

        $this->createClient($currentPublicKey);
        $this->createClient($foreignPublicKey);

        $foreignRole = new Roles();
        $foreignRole->public_key = $foreignPublicKey;
        $foreignRole->name = 'Foreign role';
        $foreignRole->id_role_in_system = 202;
        self::assertTrue($foreignRole->save());

        $service = new ManagerRoleService(new YiiRoleRepository());
        $result = $service->saveFromPost($currentPublicKey, [
            'Roles' => [
                'id' => $foreignRole->id,
                'public_key' => $currentPublicKey,
                'name' => 'Hacked role',
                'id_role_in_system' => 202,
            ],
        ]);

        self::assertSame(RoleOperationResult::FORBIDDEN, $result->status);
        self::assertSame('Foreign role', Roles::findOne($foreignRole->id)->name);
    }

    public function testDeleteForeignRoleIsForbidden(): void
    {
        $currentPublicKey = 990305;
        $foreignPublicKey = 990306;

        $this->createClient($currentPublicKey);
        $this->createClient($foreignPublicKey);

        $foreignRole = new Roles();
        $foreignRole->public_key = $foreignPublicKey;
        $foreignRole->name = 'Foreign role';
        $foreignRole->id_role_in_system = 303;
        self::assertTrue($foreignRole->save());

        $service = new ManagerRoleService(new YiiRoleRepository());
        $result = $service->deleteById((int)$foreignRole->id, $currentPublicKey);

        self::assertSame(RoleOperationResult::FORBIDDEN, $result->status);
        self::assertNotNull(Roles::findOne($foreignRole->id));
    }
}
