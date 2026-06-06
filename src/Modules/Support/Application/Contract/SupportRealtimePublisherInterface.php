<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;

interface SupportRealtimePublisherInterface
{
    public function publishMessage(SupportConversation $conversation, SupportMessage $message): void;
}
