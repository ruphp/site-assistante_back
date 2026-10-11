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
        $service = $this->service(SupportPlan::FREE);

        self::assertArrayHasKey('manager/params', $service->baseMenu(10));
        self::assertSame([['courses/index' => 'Курсы']], $service->moduleMenusForClient(10));
    }

    public function testRolesAreAvailableOnlyOnPro(): void
    {
        self::assertFalse($this->service(SupportPlan::FREE)->rolesEnabledForClient(10));
        self::assertFalse($this->service(SupportPlan::START)->rolesEnabledForClient(10));
        self::assertTrue($this->service(SupportPlan::PRO)->rolesEnabledForClient(10));
    }

    private function service(string $plan): ClientPanelMenuService
    {
        return new ClientPanelMenuService(
            new FakeClientModuleMenuRepository([['courses/index' => 'Курсы']]),
            new PanelFakeSupportSettingsRepository($plan),
        );
    }
}

final class PanelFakeSupportSettingsRepository implements SupportSettingsRepositoryInterface
{
    public function __construct(private readonly string $plan)
    {
    }

    public function getForClient(int $publicKey): SupportSettings
    {
        return new SupportSettings($publicKey, $this->plan);
    }

    public function save(SupportSettings $settings): bool
    {
        return true;
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
