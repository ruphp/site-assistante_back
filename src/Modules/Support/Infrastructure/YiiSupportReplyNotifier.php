<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportReplyNotifierInterface;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use Yii;
use yii\base\Application;
use yii\web\Response;

final class YiiSupportReplyNotifier implements SupportReplyNotifierInterface
{
    public function notifyOperatorReply(SupportConversation $conversation, SupportMessage $message): void
    {
        if ($conversation->visitorEmail === null || trim($conversation->visitorEmail) === '') {
            return;
        }

        $notify = function () use ($conversation, $message): void {
            $this->sendEmail($conversation, $message);
        };

        if (Yii::$app instanceof Application && Yii::$app->has('response')) {
            $response = Yii::$app->getResponse();
            if ($response instanceof Response) {
                $response->on(Response::EVENT_AFTER_SEND, static function () use ($notify): void {
                    $notify();
                });
                return;
            }
        }

        $notify();
    }

    private function sendEmail(SupportConversation $conversation, SupportMessage $message): void
    {
        $previousTimeout = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', '5');

        try {
            Yii::$app->mailer
                ->compose()
                ->setTo($conversation->visitorEmail)
                ->setFrom(Yii::$app->params['adminEmail'] ?? 'admin@sitewidget.ru')
                ->setSubject('Ответ службы поддержки SiteWidget')
                ->setTextBody($this->body($conversation, $message))
                ->send();
        } catch (\Throwable) {
        } finally {
            if ($previousTimeout !== false) {
                ini_set('default_socket_timeout', (string)$previousTimeout);
            }
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
