<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;

interface SupportReplyNotifierInterface
{
    public function notifyOperatorReply(SupportConversation $conversation, SupportMessage $message): void;
}
