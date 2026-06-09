<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportEntryPointRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\GetSupportWidgetStateRequest;
use app\Modules\Support\Application\Dto\SupportWidgetStateResponse;
use app\Modules\Support\Domain\SupportPlanLimit;

final class GetSupportWidgetStateUseCase implements GetSupportWidgetStateUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportEntryPointRepositoryInterface $entryPoints,
        private readonly SupportUsageRepositoryInterface $usage,
    ) {
    }

    public function get(GetSupportWidgetStateRequest $request): SupportWidgetStateResponse
    {
        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $settings = $this->settings->getForClient($request->publicKey);
        $limit = SupportPlanLimit::forPlan($settings->plan);
        $month = new \DateTimeImmutable('first day of this month 00:00:00');

        return new SupportWidgetStateResponse(
            $settings,
            $limit,
            $this->usage->monthlyConversationCount($request->publicKey, $month),
            $this->usage->monthlyMessageCount($request->publicKey, $month),
            array_slice($this->entryPoints->listForClient($request->publicKey, true), 0, $limit->maxEntryPoints),
        );
    }
}
