<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\AssistantConfigurationResponse;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use Yii;

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
        $modules = $this->withContentModules($modules, $request);

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

    private function withContentModules(array $modules, BuildAssistantConfigurationRequest $request): array
    {
        if ($this->hasInstructionsForPage($request)) {
            $modules[] = 'instructions';
        }

        if ($this->hasOnboardingForPage($request)) {
            $modules[] = 'onboarding';
        }

        return array_values(array_unique($modules));
    }

    private function hasInstructionsForPage(BuildAssistantConfigurationRequest $request): bool
    {
        if (!$this->tableExists('sw_instruction_articles')) {
            return false;
        }

        $rows = Yii::$app->db->createCommand(
            'SELECT a.id
               FROM sw_instruction_articles a
              WHERE a.public_key = :public_key
                AND a.is_active = TRUE
                AND COALESCE(a.admin_blocked, FALSE) = FALSE
              LIMIT 1',
            [':public_key' => $request->publicKey],
        )->queryColumn();

        return $rows !== [];
    }

    private function hasOnboardingForPage(BuildAssistantConfigurationRequest $request): bool
    {
        if (!$this->tableExists('sw_onboardings') || !$this->tableExists('sw_onboarding_sections')) {
            return false;
        }

        $pathname = rtrim($request->requestContext?->pathname ?? '/', '/') ?: '/';
        $pageWithQuery = $pathname . ($request->requestContext?->getparams ?? '');

        $rows = Yii::$app->db->createCommand(
            'SELECT s.url, s.include_children, s.include_query
               FROM sw_onboardings o
               JOIN sw_onboarding_sections s ON s.onboarding_id = o.id
              WHERE o.public_key = :public_key
                AND o.is_active = TRUE
                AND s.is_active = TRUE',
            [':public_key' => $request->publicKey],
        )->queryAll();

        foreach ($rows as $row) {
            $target = rtrim(trim((string)($row['url'] ?? '')), '/') ?: '/';
            $current = !empty($row['include_query']) ? $pageWithQuery : $pathname;

            if ($target === '/' || (!empty($row['include_children']) ? str_starts_with($current, $target) : $current === $target)) {
                return true;
            }
        }

        return false;
    }

    private function tableExists(string $table): bool
    {
        return Yii::$app->db->schema->getTableSchema($table, true) !== null;
    }
}
