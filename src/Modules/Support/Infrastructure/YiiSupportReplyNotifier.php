<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportReplyNotifierInterface;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use Yii;

final class YiiSupportReplyNotifier implements SupportReplyNotifierInterface
{
    public function notifyOperatorReply(SupportConversation $conversation, SupportMessage $message): void
    {
        if ($conversation->visitorEmail === null || trim($conversation->visitorEmail) === '') {
            return;
        }

        try {
            Yii::$app->mailer
                ->compose()
                ->setTo($conversation->visitorEmail)
                ->setFrom(Yii::$app->params['adminEmail'] ?? 'admin@sitewidget.ru')
                ->setSubject('Ответ службы поддержки SiteWidget')
                ->setTextBody($this->body($conversation, $message))
                ->send();
        } catch (\Throwable) {
        }
    }

    private function body(SupportConversation $conversation, SupportMessage $message): string
    {
        $lines = [
            'Здравствуйте!',
            '',
            'Оператор ответил на ваше обращение:',
            '',
            $message->body,
        ];

        return implode("\n", $lines);
    }
}
