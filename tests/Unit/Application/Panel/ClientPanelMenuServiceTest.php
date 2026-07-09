<?php

namespace tests\Unit\Application\Panel;

use app\Application\Panel\ClientPanelMenuService;
use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlan;
use app\Modules\Support\Domain\SupportSettings;
use PHPUnit\Framework\TestCase;

final class ClientPanelMenuServiceTest extends TestCase
{
    public function testReturnsBaseMenuAndDelegatesModuleMenus(): void
    {
        $service = new ClientPanelMenuService(
            new FakeClientModuleMenuRepository([
                ['courses/index' => 'Курсы'],
            ]),
            new FakeSupportSettingsRepository(SupportPlan::FREE),
        );

        self::assertArrayHasKey('manager/params', $service->baseMenu(10));
        self::assertArrayNotHasKey('manager/roles', $service->baseMenu(10));
        self::assertSame([['courses/index' => 'Курсы']], $service->moduleMenusForClient(10));
    }

    public function testShowsRolesMenuForPaidPlan(): void
    {
        $service = new ClientPanelMenuService(
            new FakeClientModuleMenuRepository([]),
            new FakeSupportSettingsRepository(SupportPlan::PRO),
        );

        self::assertArrayHasKey('manager/roles', $service->baseMenu(10));
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

final class FakeSupportSettingsRepository implements SupportSettingsRepositoryInterface
{
    public function __construct(
        private readonly string $plan,
    ) {
    }

    public function getForClient(int $publicKey): SupportSettings
    {
        return new SupportSettings($publicKey, plan: $this->plan);
    }

    public function save(SupportSettings $settings): bool
    {
        return true;
    }
}
