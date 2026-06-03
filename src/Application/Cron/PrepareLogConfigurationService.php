<?php

namespace app\Application\Cron;

use app\Application\Cron\Contract\ActiveClientProviderInterface;
use app\Application\Cron\Contract\ClientModuleCronRunnerInterface;
use app\Application\Cron\Contract\CronLogProcessorInterface;
use app\Application\Cron\Contract\CronProgressLoggerInterface;

class PrepareLogConfigurationService
{
    public function __construct(
        private readonly ActiveClientProviderInterface $clientProvider,
        private readonly ClientModuleCronRunnerInterface $moduleCronRunner,
        private readonly CronLogProcessorInterface $logProcessor,
        private readonly CronProgressLoggerInterface $logger,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function prepare(): string
    {
        $clients = $this->clientProvider->getActiveClients();

        $this->logger->write('START ALL');

        foreach ($clients as $client) {
            $publicKey = (int)$client['public_key'];
            $gmt = (int)$client['gmt'] + (-1 * (int)$_ENV['GMT_SERVERS']);

            $this->logger->write('START ' . $publicKey);

            $this->moduleCronRunner->runForClient($client);
            $this->logProcessor->processForClient($publicKey, $gmt);

            $this->logger->write('END LOG  ' . $publicKey);
        }

        $this->logger->write('END LOG  ALL');

        return '';
    }
}
