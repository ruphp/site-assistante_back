<?php

namespace app\Modules\Support\Infrastructure;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportManagerNotifierInterface;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use Yii;
use yii\httpclient\Client;

final class YiiSupportManagerNotifier implements SupportManagerNotifierInterface
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $settings,
        private readonly SupportPushDeviceRepositoryInterface $pushDevices,
        private readonly SupportPushNotificationSenderInterface $pushSender,
    ) {
    }

    public function notifyVisitorMessage(SupportConversation $conversation, SupportMessage $message): void
    {
        $settings = $this->settings->getForClient($conversation->publicKey);
        $text = $this->body($conversation, $message);

        if ($settings->notifyEmail) {
            $this->notifyEmail($conversation, $text);
        }

        if ($settings->notifyTelegram) {
            $this->notifyTelegram($settings->telegramBotToken, $settings->telegramChatId, $text);
        }

        if ($settings->notifyMax) {
            $this->notifyMax($settings->maxApiUrl, $settings->maxBotToken, $settings->maxChatId, $text);
        }

        $this->notifyPush($conversation, $message);
    }

    private function notifyEmail(SupportConversation $conversation, string $text): void
    {
        $settings = $this->settings->getForClient($conversation->publicKey);
        $emails = $this->parseEmails($settings->notificationEmails);
        if ($emails === []) {
            $emails = $this->managerEmails($conversation->publicKey);
        }

        if ($emails === []) {
            return;
        }

        try {
            Yii::$app->mailer
                ->compose()
                ->setTo($emails)
                ->setFrom(Yii::$app->params['adminEmail'] ?? 'admin@sitewidget.ru')
                ->setSubject('Новое сообщение в онлайн-поддержке SiteWidget')
                ->setTextBody($text)
                ->send();
        } catch (\Throwable) {
        }
    }

    private function parseEmails(string $rawEmails): array
    {
        $emails = preg_split('/[,;\s]+/', $rawEmails) ?: [];

        return array_values(array_unique(array_filter(
            array_map(static fn(string $email): string => strtolower(trim($email)), $emails),
            static fn(string $email): bool => filter_var($email, FILTER_VALIDATE_EMAIL) !== false,
        )));
    }

    private function notifyTelegram(string $botToken, string $chatId, string $text): void
    {
        $botToken = trim($botToken);
        $chatId = trim($chatId);
        if ($botToken === '' || $chatId === '') {
            return;
        }

        try {
            (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.telegram.org/bot' . $botToken . '/sendMessage')
                ->setData([
                    'chat_id' => $chatId,
                    'text' => $text,
                    'disable_web_page_preview' => true,
                ])
                ->send();
        } catch (\Throwable) {
        }
    }

    private function notifyMax(string $apiUrl, string $botToken, string $chatId, string $text): void
    {
        $apiUrl = rtrim(trim($apiUrl), '/');
        $botToken = trim($botToken);
        $chatId = trim($chatId);
        if ($apiUrl === '' || $botToken === '' || $chatId === '') {
            return;
        }

        try {
            (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl($apiUrl . '/messages')
                ->addHeaders(['Authorization' => 'Bearer ' . $botToken])
                ->setFormat(Client::FORMAT_JSON)
                ->setData([
                    'chat_id' => $chatId,
                    'text' => $text,
                ])
                ->send();
        } catch (\Throwable) {
        }
    }

    private function managerEmails(int $publicKey): array
    {
        $rows = Users::find()
            ->select('users.email')
            ->innerJoin('auth_assignment', 'auth_assignment.user_id = users.id')
            ->where([
                'users.public_key' => $publicKey,
                'users.status' => 1,
                'auth_assignment.item_name' => 'manager',
            ])
            ->asArray()
            ->all();

        $emails = [];
        foreach ($rows as $row) {
            $email = trim((string)($row['email'] ?? ''));
            if ($email !== '') {
                $emails[] = $email;
            }
        }

        return array_values(array_unique($emails));
    }

    private function body(SupportConversation $conversation, SupportMessage $message): string
    {
        $lines = [
            'Новое сообщение от посетителя.',
            '',
            'Клиент: ' . $conversation->publicKey,
            'Диалог: ' . $conversation->id,
        ];

        if ($conversation->visitorEmail !== null && trim($conversation->visitorEmail) !== '') {
            $lines[] = 'Email посетителя: ' . $conversation->visitorEmail;
        }

        if ($conversation->visitorName !== null && trim($conversation->visitorName) !== '') {
            $lines[] = 'Имя посетителя: ' . $conversation->visitorName;
        }

        if ($conversation->pageUrl !== null && trim($conversation->pageUrl) !== '') {
            $lines[] = 'Страница: ' . $conversation->pageUrl;
        }

        $lines[] = '';
        $lines[] = 'Сообщение:';
        $lines[] = $message->body;

        return implode("\n", $lines);
    }

    private function notifyPush(SupportConversation $conversation, SupportMessage $message): void
    {
        $tokens = $this->pushDevices->activeTokensForClient($conversation->publicKey);
        if ($tokens === []) {
            return;
        }

        $title = 'SiteWidget';
        $visitor = $this->pushConversationLabel($conversation);
        $snippet = trim((string)$message->body);
        if (function_exists('mb_substr')) {
            $snippet = mb_substr($snippet, 0, 120);
        } elseif (strlen($snippet) > 120) {
            $snippet = substr($snippet, 0, 120);
        }

        $body = $visitor !== '' ? $visitor . ': ' . $snippet : $snippet;

        foreach ($tokens as $token) {
            $this->pushSender->sendToToken($token, $title, $body, [
                'conversation_id' => (string)$conversation->id,
                'public_key' => (string)$conversation->publicKey,
                'visitor_id' => (string)$conversation->visitorId,
                'visitor_name' => (string)($conversation->visitorName ?? ''),
                'visitor_email' => (string)($conversation->visitorEmail ?? ''),
                'last_message_at' => (string)($conversation->lastMessageAt ?? $message->createdAt),
                'sender_type' => (string)$message->senderType,
            ]);
        }
    }

    private function pushConversationLabel(SupportConversation $conversation): string
    {
        $label = trim((string)($conversation->visitorName ?? ''));
        if ($label !== '') {
            return $label;
        }

        $label = trim((string)($conversation->visitorEmail ?? ''));
        if ($label !== '') {
            return $label;
        }

        return 'Диалог #' . $conversation->id;
    }
}
