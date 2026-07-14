<?php

namespace app\Application\Assistant\UseCase;

use app\Application\Assistant\AssistantAccessGuard;
use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\AssistantConfigurationResponse;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Modules\Instructions\Domain\InstructionPlanLimit;
use app\Modules\Instructions\Domain\InstructionsModule;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleUrlRecord;
use app\Modules\Onboarding\Domain\OnboardingPlanLimit;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintUrlRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
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
        $supportSettings = $this->supportSettingsRepository->getForClient($client->publicKey);
        $plan = SupportPlan::normalize($supportSettings->plan);
        $modules = $this->allowedEnabledModules($client->publicKey, $client->enabledModules());
        $modules = $this->withTariffModules($modules, $plan);
        $modules = $this->filterModulesForPlanAndPage($modules, $supportSettings, $request);

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

    private function withTariffModules(array $modules, string $plan): array
    {
        $modules[] = InstructionsModule::NAME;

        if (in_array($plan, [SupportPlan::START, SupportPlan::PRO], true)) {
            $modules[] = 'onboarding';
            $modules[] = 'hints';
            $modules[] = 'surveys';
            $modules[] = 'polls';
        }

        return array_values(array_unique($modules));
    }

    private function filterModulesForPlanAndPage(array $modules, SupportSettings $settings, BuildAssistantConfigurationRequest $request): array
    {
        $plan = SupportPlan::normalize($settings->plan);
        $instructionLimit = InstructionPlanLimit::forPlan($plan);
        $onboardingLimit = OnboardingPlanLimit::forPlan($plan);

        return array_values(array_filter($modules, function (string $module) use ($plan, $instructionLimit, $onboardingLimit, $request): bool {
            if ($module === InstructionsModule::NAME) {
                return $instructionLimit->enabled && $this->hasInstructionsForPage($request, $instructionLimit->urlBindingsEnabled);
            }

            if ($module === 'onboarding') {
                return $onboardingLimit->enabled && $this->hasOnboardingsForPage($request, $onboardingLimit->urlBindingsEnabled);
            }

            if (in_array($module, ['surveys', 'polls'], true)) {
                return in_array($plan, [SupportPlan::START, SupportPlan::PRO], true);
            }

            if ($module === 'hints') {
                return $onboardingLimit->enabled && $this->hasHintsForPage($request, $onboardingLimit->urlBindingsEnabled);
            }

            return true;
        }));
    }

    private function hasInstructionsForPage(BuildAssistantConfigurationRequest $request, bool $urlBindingsEnabled): bool
    {
        $articleIds = InstructionArticleRecord::find()
            ->where(['public_key' => $request->publicKey, 'is_active' => true, 'admin_blocked' => false])
            ->select('id')
            ->column();
        if ($articleIds === []) {
            return false;
        }
        if (!$urlBindingsEnabled) {
            return true;
        }

        $boundArticleIds = InstructionArticleUrlRecord::find()
            ->where(['article_id' => $articleIds])
            ->select('article_id')
            ->distinct()
            ->column();
        if (count($boundArticleIds) < count($articleIds)) {
            return true;
        }

        $pathname = $request->requestContext?->pathname ?? '';
        $pageUrl = $pathname . ($request->requestContext?->getparams ?? '');
        foreach (InstructionArticleUrlRecord::find()->where(['article_id' => $articleIds])->all() as $url) {
            $target = rtrim((string)$url->url, '/');
            $current = rtrim($url->include_query ? $pageUrl : $pathname, '/');
            if ($target === '') {
                continue;
            }
            if ($url->include_children ? strpos($current, $target) === 0 : $current === $target) {
                return true;
            }
        }

        return false;
    }

    private function hasOnboardingsForPage(BuildAssistantConfigurationRequest $request, bool $urlBindingsEnabled): bool
    {
        $onboardingIds = OnboardingRecord::find()
            ->where(['public_key' => $request->publicKey, 'is_active' => true])
            ->select('id')
            ->column();
        if ($onboardingIds === []) {
            return false;
        }

        $sections = OnboardingSectionRecord::find()
            ->where(['onboarding_id' => $onboardingIds, 'is_active' => true])
            ->all();

        foreach ($sections as $section) {
            if ($section->url === '' || $this->urlMatchesRequest(
                $request,
                (string)$section->url,
                $urlBindingsEnabled && (bool)$section->include_children,
                $urlBindingsEnabled && (bool)$section->include_query,
            )) {
                return true;
            }
        }

        return false;
    }

    private function hasHintsForPage(BuildAssistantConfigurationRequest $request, bool $urlBindingsEnabled): bool
    {
        $hintIds = OnboardingHintRecord::find()
            ->where(['public_key' => $request->publicKey, 'is_active' => true, 'standalone_enabled' => true])
            ->select('id')
            ->column();
        if ($hintIds === []) {
            return false;
        }
        $boundHintIds = OnboardingHintUrlRecord::find()
            ->where(['hint_id' => $hintIds])
            ->select('hint_id')
            ->distinct()
            ->column();
        if (count($boundHintIds) < count($hintIds)) {
            return true;
        }

        foreach (OnboardingHintUrlRecord::find()->where(['hint_id' => $hintIds])->all() as $url) {
            if ($this->urlMatchesRequest(
                $request,
                (string)$url->url,
                $urlBindingsEnabled && (bool)$url->include_children,
                $urlBindingsEnabled && (bool)$url->include_query,
            )) {
                return true;
            }
        }

        return false;
    }

    private function urlMatchesRequest(BuildAssistantConfigurationRequest $request, string $target, bool $includeChildren, bool $includeQuery): bool
    {
        $target = rtrim(trim($target), '/');
        if ($target === '') {
            return true;
        }

        $pathname = $request->requestContext?->pathname ?? '';
        $pageUrl = $pathname . ($request->requestContext?->getparams ?? '');
        $current = rtrim($includeQuery ? $pageUrl : $pathname, '/');

        return $includeChildren ? strpos($current, $target) === 0 : $current === $target;
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
