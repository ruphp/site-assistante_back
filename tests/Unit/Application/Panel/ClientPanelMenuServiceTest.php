<?php

namespace tests\Unit\Application\Panel;

use app\Application\Panel\ClientPanelMenuService;
use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;
use PHPUnit\Framework\TestCase;

final class ClientPanelMenuServiceTest extends TestCase
{
    public function testReturnsBaseMenuAndDelegatesModuleMenus(): void
    {
        $service = new ClientPanelMenuService(new FakeClientModuleMenuRepository([
            ['courses/index' => 'Курсы'],
        ]));

        self::assertArrayHasKey('manager/params', $service->baseMenu());
        self::assertSame([['courses/index' => 'Курсы']], $service->moduleMenusForClient(10));
    }
}

final class FakeClientModuleMenuRepository implements ClientModuleMenuRepositoryInterface
{
    public function __construct(
        private readonly array $menus,
    ) {
    }

    public function getModuleMenusForClient(int $publicKey): array
    {
        return $this->menus;
    }
}
