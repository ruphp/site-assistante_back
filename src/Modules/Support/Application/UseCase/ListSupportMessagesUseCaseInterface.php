<?php

namespace app\Modules\Support\Application\UseCase;

use app\Modules\Support\Application\Dto\ListSupportMessagesRequest;
use app\Modules\Support\Application\Dto\SupportMessageListResponse;

interface ListSupportMessagesUseCaseInterface
{
    public function list(ListSupportMessagesRequest $request): SupportMessageListResponse;
}
