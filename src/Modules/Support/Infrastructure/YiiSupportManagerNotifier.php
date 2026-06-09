<?php

namespace app\Modules\Support\Infrastructure;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportManagerNotifierInterface;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use Yii;
use yii\httpclient\Client;

final class YiiSupportManagerNotifier implements SupportManagerNotifierInterface
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $settings,
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

        if ($conversation->pageUrl !== null && trim($conversation->pageUrl) !== '') {
            $lines[] = 'Страница: ' . $conversation->pageUrl;
        }

        $lines[] = '';
        $lines[] = 'Сообщение:';
        $lines[] = $message->body;

        return implode("\n", $lines);
    }
}
