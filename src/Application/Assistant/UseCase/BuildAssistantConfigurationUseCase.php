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
use app\Modules\Surveys\Domain\SurveyPlanLimit;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyFormRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyResponseRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyUrlRecord;
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
        $moduleAccess = $this->moduleAccess($modules, $supportSettings, $request);
        $modules = $this->availableModules($modules, $moduleAccess);

        $response = new AssistantConfigurationResponse(
            error: [],
            position: $client->params['leftbutton'] ? 'left' : 'right',
            run: $client->params['run'],
            theme: $client->params['design'],
            domain: $client->allowedHosts(),
            typeTickets: 0,
            textContacts: $client->params['tab_tp_contacts'] ? $client->params['tp_contacts'] : '',
            zeroLogDelay: $client->params['timeout'],
            urlSiteWidgetTp: $client->params['server_stp'],
            modules: array_values($modules),
            moduleAccess: $moduleAccess,
            plan: $this->planPayload($supportSettings),
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
            $modules[] = 'polls';
        }

        return array_values(array_unique($modules));
    }

    private function moduleAccess(array $modules, SupportSettings $settings, BuildAssistantConfigurationRequest $request): array
    {
        $plan = SupportPlan::normalize($settings->plan);
        $instructionLimit = InstructionPlanLimit::forPlan($plan);
        $onboardingLimit = OnboardingPlanLimit::forPlan($plan);
        $surveyLimit = SurveyPlanLimit::forPlan($plan);

        $access = [];
        foreach (['support', InstructionsModule::NAME, 'onboarding', 'hints', 'polls'] as $module) {
            $configured = in_array($module, $modules, true);
            $available = $configured;
            $reason = $configured ? null : 'disabled';

            if ($module === InstructionsModule::NAME) {
                $available = $configured && $instructionLimit->enabled;
                $reason = $this->moduleReason($configured, $instructionLimit->enabled, $available);
                if ($available && !$this->hasInstructionsForPage($request, $instructionLimit->urlBindingsEnabled)) {
                    $available = false;
                    $reason = 'empty_for_page';
                }
            } elseif ($module === 'onboarding') {
                $available = $configured && $onboardingLimit->enabled;
                $reason = $this->moduleReason($configured, $onboardingLimit->enabled, $available);
                if ($available && !$this->hasOnboardingsForPage($request, $onboardingLimit->urlBindingsEnabled)) {
                    $available = false;
                    $reason = 'empty_for_page';
                }
            } elseif ($module === 'hints') {
                $available = $configured && $onboardingLimit->enabled;
                $reason = $this->moduleReason($configured, $onboardingLimit->enabled, $available);
                if ($available && !$this->hasHintsForPage($request, $onboardingLimit->urlBindingsEnabled)) {
                    $available = false;
                    $reason = 'empty_for_page';
                }
            } elseif ($module === 'polls') {
                $available = $configured && $surveyLimit->enabled;
                $reason = $this->moduleReason($configured, $surveyLimit->enabled, $available);
                if ($available && !$this->hasSurveysForPage($request, $surveyLimit->urlBindingsEnabled)) {
                    $available = false;
                    $reason = 'empty_for_page';
                }
            }

            $access[$module] = [
                'enabled' => $configured,
                'available' => $available,
                'reason' => $available ? null : $reason,
            ];
        }

        return $access;
    }

    private function availableModules(array $modules, array $moduleAccess): array
    {
        return array_values(array_filter(
            $modules,
            static fn(string $module): bool => (bool)($moduleAccess[$module]['available'] ?? true)
        ));
    }

    private function moduleReason(bool $configured, bool $planEnabled, bool $available): ?string
    {
        if ($available) {
            return null;
        }
        if (!$configured) {
            return 'disabled';
        }
        if (!$planEnabled) {
            return 'plan_unavailable';
        }

        return 'unavailable';
    }

    private function planPayload(SupportSettings $settings): array
    {
        return [
            'effective' => SupportPlan::normalize($settings->plan),
            'source' => $settings->isTrialActive() ? 'trial' : 'plan',
            'expiresAt' => $settings->planExpiresAt,
        ];
    }

    private function hasSurveysForPage(BuildAssistantConfigurationRequest $request, bool $urlBindingsEnabled): bool
    {
        if ($this->hasDelayedSurveyForVisitor($request)) {
            return true;
        }

        $today = date('Y-m-d');
        $surveyIds = SurveyFormRecord::find()
            ->where(['public_key' => $request->publicKey, 'is_active' => true])
            ->andWhere(['or', ['date_start' => null], ['<=', 'date_start', $today]])
            ->andWhere(['or', ['date_finish' => null], ['>=', 'date_finish', $today]])
            ->select('id')
            ->column();
        if ($surveyIds === []) {
            return false;
        }
        $surveyIds = SurveyQuestionRecord::find()
            ->where(['survey_id' => $surveyIds])
            ->select('survey_id')
            ->distinct()
            ->column();
        if ($surveyIds === []) {
            return false;
        }
        if (!$urlBindingsEnabled) {
            return true;
        }

        $boundSurveyIds = SurveyUrlRecord::find()
            ->where(['survey_id' => $surveyIds])
            ->select('survey_id')
            ->distinct()
            ->column();
        if (count($boundSurveyIds) < count($surveyIds)) {
            return true;
        }

        foreach (SurveyUrlRecord::find()->where(['survey_id' => $surveyIds])->all() as $url) {
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

    private function hasDelayedSurveyForVisitor(BuildAssistantConfigurationRequest $request): bool
    {
        $visitorKey = $this->visitorKey($request);

        $surveyIds = SurveyResponseRecord::find()
            ->where([
                'public_key' => $request->publicKey,
                'visitor_key' => $visitorKey,
                'is_delayed' => true,
                'completed_at' => null,
            ])
            ->select('survey_id')
            ->column();
        if ($surveyIds === []) {
            return false;
        }

        return SurveyFormRecord::find()
            ->where(['id' => $surveyIds, 'public_key' => $request->publicKey, 'is_active' => true])
            ->exists();
    }

    private function visitorKey(BuildAssistantConfigurationRequest $request): string
    {
        $userId = (string)($request->requestContext?->userId ?? '');
        if ($userId !== '' && $userId !== '0') {
            return 'user:' . $userId;
        }

        $visitorId = (string)($request->requestContext?->visitorId ?? $request->requestContext?->remoteAddr ?? '');

        return 'anon:' . sha1($visitorId);
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
