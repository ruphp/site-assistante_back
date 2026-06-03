<?php

namespace tests\Unit\Application\Cron;

use app\Application\Cron\Contract\ActiveClientProviderInterface;
use app\Application\Cron\Contract\ClientModuleCronRunnerInterface;
use app\Application\Cron\Contract\CronLogProcessorInterface;
use app\Application\Cron\Contract\CronProgressLoggerInterface;
use app\Application\Cron\PrepareLogConfigurationService;
use PHPUnit\Framework\TestCase;

final class PrepareLogConfigurationServiceTest extends TestCase
{
    public function testRunsModuleCronsAndProcessesLogsForEveryActiveClient(): void
    {
        $_ENV['GMT_SERVERS'] = '2';
        $clients = [
            ['public_key' => 10, 'gmt' => 5],
            ['public_key' => 20, 'gmt' => 1],
        ];
        $runner = new FakeClientModuleCronRunner();
        $processor = new FakeCronLogProcessor();
        $logger = new FakeCronProgressLogger();

        $service = new PrepareLogConfigurationService(
            new FakeActiveClientProvider($clients),
            $runner,
            $processor,
            $logger,
        );

        self::assertSame('', $service->prepare());
        self::assertSame($clients, $runner->clients);
        self::assertSame([[10, 3], [20, -1]], $processor->calls);
        self::assertSame([
            'START ALL',
            'START 10',
            'END LOG  10',
            'START 20',
            'END LOG  20',
            'END LOG  ALL',
        ], $logger->messages);
    }
}

final class FakeActiveClientProvider implements ActiveClientProviderInterface
{
    public function __construct(
        private readonly array $clients,
    ) {
    }

    public function getActiveClients(): array
    {
        return $this->clients;
    }
}

final class FakeClientModuleCronRunner implements ClientModuleCronRunnerInterface
{
    public array $clients = [];

    public function runForClient(array $client): void
    {
        $this->clients[] = $client;
    }
}

final class FakeCronLogProcessor implements CronLogProcessorInterface
{
    public array $calls = [];

    public function processForClient(int $publicKey, int $gmt): void
    {
        $this->calls[] = [$publicKey, $gmt];
    }
}

final class FakeCronProgressLogger implements CronProgressLoggerInterface
{
    public array $messages = [];

    public function write(string $message, string $level = 'INFO'): void
    {
        $this->messages[] = $message;
    }
}
