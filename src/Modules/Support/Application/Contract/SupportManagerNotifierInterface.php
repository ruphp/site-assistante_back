<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;

interface SupportManagerNotifierInterface
{
    public function notifyVisitorMessage(SupportConversation $conversation, SupportMessage $message): void;
}
