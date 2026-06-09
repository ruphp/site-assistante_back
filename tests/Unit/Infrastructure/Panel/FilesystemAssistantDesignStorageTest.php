<?php

namespace tests\Unit\Infrastructure\Panel;

use app\Infrastructure\Panel\FilesystemAssistantDesignStorage;
use PHPUnit\Framework\TestCase;

final class FilesystemAssistantDesignStorageTest extends TestCase
{
    private int $publicKey = 990777;

    protected function tearDown(): void
    {
        @unlink(dirname(__DIR__, 4) . '/web/custom/custom_' . $this->publicKey . '.css');
        @unlink(dirname(__DIR__, 4) . '/web/custom/logo' . $this->publicKey . '.svg');

        parent::tearDown();
    }

    public function testCreatesDesignFilesInsideProjectWebCustomDirectory(): void
    {
        $storage = new FilesystemAssistantDesignStorage();

        $storage->ensureFiles($this->publicKey);

        self::assertFileExists(dirname(__DIR__, 4) . '/web/custom/custom_' . $this->publicKey . '.css');
        self::assertFileExists(dirname(__DIR__, 4) . '/web/custom/logo' . $this->publicKey . '.svg');
    }
}
