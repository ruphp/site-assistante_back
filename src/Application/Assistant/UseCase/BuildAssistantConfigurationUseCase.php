<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\AssistantConfigurationResponse;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;

final class BuildAssistantConfigurationUseCase implements BuildAssistantConfigurationUseCaseInterface
{
    private readonly AssistantContextRepositoryInterface $assistantContextRepository;
    private readonly AssistantConfigurationLoggerInterface $configurationLogger;
    private readonly AssistantAccessGuard $accessGuard;
    private readonly ClientModuleAccessRepositoryInterface $moduleAccessRepository;

    public function __construct(
        AssistantContextRepositoryInterface $assistantContextRepository,
        AssistantConfigurationLoggerInterface $configurationLogger,
        ClientModuleAccessRepositoryInterface $moduleAccessRepository,
        ?AssistantAccessGuard $accessGuard = null,
    ) {
        $this->assistantContextRepository = $assistantContextRepository;
        $this->configurationLogger = $configurationLogger;
        $this->moduleAccessRepository = $moduleAccessRepository;
        $this->accessGuard = $accessGuard ?? new AssistantAccessGuard();
    }

    public function build(BuildAssistantConfigurationRequest $request): AssistantConfigurationResponse
    {
        $context = $this->assistantContextRepository->getByPublicKey($request->publicKey, $request->requestContext);
        $this->accessGuard->assertAllowed($context, $request->requestContext);
        $client = $context->client;
        $modules = $this->allowedEnabledModules($client->publicKey, $client->enabledModules());
        $support = $this->resolveSupportModule($modules);

        if ($support !== 0) {
            $ticketTypes = $this->ticketTypes();
            $nameSupport = $ticketTypes[$support];
            unset($modules[array_search($nameSupport, $modules, true)]);
            $modules[] = 'support';
        }

        $response = new AssistantConfigurationResponse(
            error: [],
            position: $client->params['leftbutton'] ? 'left' : 'right',
            run: $client->params['run'],
            theme: $client->params['design'],
            domain: $client->allowedHosts(),
            typeTickets: $support,
            textContacts: $client->params['tab_tp_contacts'] ? $client->params['tp_contacts'] : '',
            zeroLogDelay: $client->params['timeout'],
            urlSmguideTp: $client->params['server_stp'],
            modules: array_values($modules),
        );

        try {
            $this->configurationLogger->log($request, $context);
        } catch (\Exception $e) {
            return $response->withRedisError();
        }

        return $response;
    }

    private function allowedEnabledModules(int $publicKey, array $enabledModules): array
    {
        return $this->moduleAccessRepository->getForClient($publicKey)->filterAllowed($enabledModules);
    }

    private function resolveSupportModule(array $modules): int
    {
        $ticketType = array_intersect($this->ticketTypes(), $modules);

        return count($ticketType) ? array_key_first($ticketType) : 0;
    }

    private function ticketTypes(): array
    {
        return [
            1 => 'tpsmartius',
            2 => 'tpotrs',
        ];
    }
}
