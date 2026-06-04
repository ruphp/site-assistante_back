<?php

use app\Application\Admin\Contract\ClientAccessRepositoryInterface;
use app\Application\Admin\Contract\ClientRepositoryInterface;
use app\Application\Client\Contract\ClientModuleAccessRepositoryInterface;
use app\Application\Cron\Contract\ActiveClientProviderInterface;
use app\Application\Cron\Contract\ClientModuleCronRunnerInterface;
use app\Application\Cron\Contract\CronLogProcessorInterface;
use app\Application\Cron\Contract\CronProgressLoggerInterface;
use app\Application\Monitoring\Contract\ChartProviderInterface;
use app\Application\Monitoring\Contract\ModuleContentCounterInterface;
use app\Application\Role\Contract\RoleRepositoryInterface;
use app\Application\Assistant\Contract\AssistantConfigurationLoggerInterface;
use app\Application\Assistant\Contract\AssistantContextRepositoryInterface;
use app\Application\Assistant\Contract\AssistantEventLoggerInterface;
use app\Application\Assistant\Contract\AssistantRateLimiterInterface;
use app\Application\Assistant\UseCase\BuildAssistantConfigurationUseCase;
use app\Application\Assistant\UseCase\BuildAssistantConfigurationUseCaseInterface;
use app\Application\Assistant\UseCase\LogAssistantOpenUseCase;
use app\Application\Assistant\UseCase\LogAssistantOpenUseCaseInterface;
use app\Application\Panel\Contract\AssistantDesignStorageInterface;
use app\Application\Panel\Contract\AssistantParamsRepositoryInterface;
use app\Application\Panel\Contract\ClientModuleMenuRepositoryInterface;
use app\Application\Panel\Metrics\Contract\PanelModuleMetricChartRepositoryInterface;
use app\Application\User\Contract\UserAccountServiceInterface;
use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportMessageRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportReplyNotifierInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\UseCase\GetSupportWidgetStateUseCase;
use app\Modules\Support\Application\UseCase\GetSupportWidgetStateUseCaseInterface;
use app\Modules\Support\Application\UseCase\ListSupportMessagesUseCase;
use app\Modules\Support\Application\UseCase\ListSupportMessagesUseCaseInterface;
use app\Modules\Support\Application\UseCase\ManageSupportSettingsUseCase;
use app\Modules\Support\Application\UseCase\OperatorSupportUseCase;
use app\Modules\Support\Application\UseCase\SendSupportMessageUseCase;
use app\Modules\Support\Application\UseCase\SendSupportMessageUseCaseInterface;
use app\Modules\Support\Application\UseCase\StartSupportConversationUseCase;
use app\Modules\Support\Application\UseCase\StartSupportConversationUseCaseInterface;
use app\Modules\Support\Infrastructure\YiiSupportConversationRepository;
use app\Modules\Support\Infrastructure\YiiSupportMessageRepository;
use app\Modules\Support\Infrastructure\YiiSupportReplyNotifier;
use app\Modules\Support\Infrastructure\YiiSupportSettingsRepository;
use app\Modules\Support\Infrastructure\YiiSupportUsageRepository;
use app\Infrastructure\Admin\YiiClientAccessRepository;
use app\Infrastructure\Admin\YiiClientRepository;
use app\Infrastructure\Client\YiiRbacClientModuleAccessRepository;
use app\Infrastructure\Cron\FileCronProgressLogger;
use app\Infrastructure\Cron\YiiActiveClientProvider;
use app\Infrastructure\Cron\YiiClientModuleCronRunner;
use app\Infrastructure\Cron\YiiRedisCronLogProcessor;
use app\Infrastructure\Monitoring\YiiChartProvider;
use app\Infrastructure\Monitoring\YiiModuleContentCounter;
use app\Infrastructure\Role\YiiRoleRepository;
use app\Infrastructure\User\YiiUserAccountService;
use app\Infrastructure\Assistant\RedisAssistantEventLogger;
use app\Infrastructure\Assistant\RedisAssistantConfigurationLogger;
use app\Infrastructure\Assistant\RedisAssistantRateLimiter;
use app\Infrastructure\Assistant\YiiAssistantContextRepository;
use app\Infrastructure\Panel\YiiAssistantParamsRepository;
use app\Infrastructure\Panel\FilesystemAssistantDesignStorage;
use app\Infrastructure\Panel\YiiRbacClientModuleMenuRepository;
use app\Infrastructure\Panel\YiiPanelModuleMetricChartRepository;

return [
    'definitions' => [
        ClientRepositoryInterface::class => YiiClientRepository::class,
        ClientAccessRepositoryInterface::class => YiiClientAccessRepository::class,
        ClientModuleAccessRepositoryInterface::class => YiiRbacClientModuleAccessRepository::class,
        ActiveClientProviderInterface::class => YiiActiveClientProvider::class,
        ClientModuleCronRunnerInterface::class => YiiClientModuleCronRunner::class,
        CronLogProcessorInterface::class => YiiRedisCronLogProcessor::class,
        CronProgressLoggerInterface::class => FileCronProgressLogger::class,
        ChartProviderInterface::class => YiiChartProvider::class,
        ModuleContentCounterInterface::class => YiiModuleContentCounter::class,
        RoleRepositoryInterface::class => YiiRoleRepository::class,
        BuildAssistantConfigurationUseCaseInterface::class => BuildAssistantConfigurationUseCase::class,
        LogAssistantOpenUseCaseInterface::class => LogAssistantOpenUseCase::class,
        AssistantContextRepositoryInterface::class => YiiAssistantContextRepository::class,
        AssistantConfigurationLoggerInterface::class => RedisAssistantConfigurationLogger::class,
        AssistantEventLoggerInterface::class => RedisAssistantEventLogger::class,
        AssistantRateLimiterInterface::class => RedisAssistantRateLimiter::class,
        AssistantParamsRepositoryInterface::class => YiiAssistantParamsRepository::class,
        AssistantDesignStorageInterface::class => FilesystemAssistantDesignStorage::class,
        ClientModuleMenuRepositoryInterface::class => YiiRbacClientModuleMenuRepository::class,
        PanelModuleMetricChartRepositoryInterface::class => YiiPanelModuleMetricChartRepository::class,
        UserAccountServiceInterface::class => YiiUserAccountService::class,
        SupportSettingsRepositoryInterface::class => YiiSupportSettingsRepository::class,
        SupportConversationRepositoryInterface::class => YiiSupportConversationRepository::class,
        SupportMessageRepositoryInterface::class => YiiSupportMessageRepository::class,
        SupportReplyNotifierInterface::class => YiiSupportReplyNotifier::class,
        SupportUsageRepositoryInterface::class => YiiSupportUsageRepository::class,
        GetSupportWidgetStateUseCaseInterface::class => GetSupportWidgetStateUseCase::class,
        StartSupportConversationUseCaseInterface::class => StartSupportConversationUseCase::class,
        SendSupportMessageUseCaseInterface::class => SendSupportMessageUseCase::class,
        ListSupportMessagesUseCaseInterface::class => ListSupportMessagesUseCase::class,
        ManageSupportSettingsUseCase::class => ManageSupportSettingsUseCase::class,
        OperatorSupportUseCase::class => OperatorSupportUseCase::class,
    ],
];
