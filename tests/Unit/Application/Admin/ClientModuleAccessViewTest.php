<?php

namespace tests\Unit\Application\Admin;

use app\Application\Admin\Dto\ClientModuleAccessView;
use app\Application\Admin\Dto\ClientModuleAccessViewItem;
use PHPUnit\Framework\TestCase;

final class ClientModuleAccessViewTest extends TestCase
{
    public function testBuildsLegacyArraysForCurrentAdminView(): void
    {
        $view = new ClientModuleAccessView([
            new ClientModuleAccessViewItem('courses', 'Курсы', true),
            new ClientModuleAccessViewItem('chatbots', 'Чат-боты', false),
        ]);

        self::assertSame([
            'courses' => true,
            'chatbots' => false,
        ], $view->selectedModules());

        self::assertSame([
            'courses' => 'Курсы',
            'chatbots' => 'Чат-боты',
        ], $view->moduleLabels());

        self::assertCount(2, $view->items());
    }
}
