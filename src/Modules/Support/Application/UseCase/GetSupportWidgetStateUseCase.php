<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportUsageRepositoryInterface;
use app\Modules\Support\Application\Dto\GetSupportWidgetStateRequest;
use app\Modules\Support\Application\Dto\SupportWidgetStateResponse;
use app\Modules\Support\Domain\SupportPlanLimit;

final class GetSupportWidgetStateUseCase implements GetSupportWidgetStateUseCaseInterface
{
    public function __construct(
        private readonly SupportAccessGuard $accessGuard,
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportUsageRepositoryInterface $usage,
    ) {
    }

    public function get(GetSupportWidgetStateRequest $request): SupportWidgetStateResponse
    {
        $this->accessGuard->assertAvailable($request->publicKey, $request->context);
        $limit = SupportPlanLimit::free();
        $month = new \DateTimeImmutable('first day of this month 00:00:00');

        return new SupportWidgetStateResponse(
            $this->settings->getForClient($request->publicKey),
            $limit,
            $this->usage->monthlyConversationCount($request->publicKey, $month),
            $this->usage->monthlyMessageCount($request->publicKey, $month),
        );
    }
}
