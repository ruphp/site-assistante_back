<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\AssistantConfigurationResponse;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportPlan;

final class BuildAssistantConfigurationUseCase implements BuildAssistantConfigurationUseCaseInterface
{
    private readonly AssistantContextRepositoryInterface $assistantContextRepository;
    private readonly AssistantConfigurationLoggerInterface $configurationLogger;
    private readonly AssistantAccessGuard $accessGuard;
    private readonly ClientModuleAccessRepositoryInterface $moduleAccessRepository;
    private readonly SupportSettingsRepositoryInterface $supportSettingsRepository;

    public function __construct(
        AssistantContextRepositoryInterface $assistantContextRepository,
        AssistantConfigurationLoggerInterface $configurationLogger,
        ClientModuleAccessRepositoryInterface $moduleAccessRepository,
        SupportSettingsRepositoryInterface $supportSettingsRepository,
        ?AssistantAccessGuard $accessGuard = null,
    ) {
        $this->assistantContextRepository = $assistantContextRepository;
        $this->configurationLogger = $configurationLogger;
        $this->moduleAccessRepository = $moduleAccessRepository;
        $this->supportSettingsRepository = $supportSettingsRepository;
        $this->accessGuard = $accessGuard ?? new AssistantAccessGuard();
    }

    public function build(BuildAssistantConfigurationRequest $request): AssistantConfigurationResponse
    {
        $context = $this->assistantContextRepository->getByPublicKey($request->publicKey, $request->requestContext);
        $this->accessGuard->assertAllowed($context, $request->requestContext);
        $client = $context->client;
        $modules = $this->allowedEnabledModules($client->publicKey, $client->enabledModules());
        $supportSettings = $this->supportSettingsRepository->getForClient($client->publicKey);

        $response = new AssistantConfigurationResponse(
            error: [],
            position: $client->params['leftbutton'] ? 'left' : 'right',
            run: $client->params['run'],
            theme: $client->params['design'],
            domain: $client->allowedHosts(),
            typeTickets: 0,
            textContacts: $client->params['tab_tp_contacts'] ? $client->params['tp_contacts'] : '',
            zeroLogDelay: $client->params['timeout'],
            urlSmguideTp: $client->params['server_stp'],
            modules: array_values($modules),
            autoOpenSnoozeMinutes: $supportSettings->autoOpenSnoozeMinutes,
            branding: $this->brandingForSettings($supportSettings),
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

    private function brandingForSettings(SupportSettings $settings): array
    {
        $isFree = SupportPlan::normalize($settings->plan) === SupportPlan::FREE;

        return [
            'enabled' => $isFree || $settings->showBranding,
            'can_disable' => !$isFree,
        ];
    }
}
