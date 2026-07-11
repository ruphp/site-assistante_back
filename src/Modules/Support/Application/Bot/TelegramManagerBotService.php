<?php

namespace app\Modules\Support\Application\Bot;

use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Application\UseCase\OperatorSupportUseCase;
use app\Modules\Support\Domain\SupportConversation;
use app\Modules\Support\Domain\SupportMessage;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportTelegramManagerCodeRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportTelegramManagerLinkRecord;
use Yii;
use yii\httpclient\Client;

final class TelegramManagerBotService
{
    public function __construct(
        private readonly OperatorSupportUseCase $operatorSupport,
    ) {
    }

    public function createLinkCode(int $userId, int $publicKey): string
    {
        SupportTelegramManagerCodeRecord::updateAll(
            ['used_at' => new \yii\db\Expression('NOW()')],
            ['user_id' => $userId, 'public_key' => $publicKey, 'used_at' => null],
        );

        do {
            $code = 'SW-' . random_int(100000, 999999);
        } while (SupportTelegramManagerCodeRecord::find()->where(['code' => $code])->exists());

        $record = new SupportTelegramManagerCodeRecord();
        $record->user_id = $userId;
        $record->public_key = $publicKey;
        $record->code = $code;
        $record->expires_at = (new \DateTimeImmutable('+30 minutes'))->format('Y-m-d H:i:s');
        $record->save(false);

        return $code;
    }

    public function handleUpdate(array $update): void
    {
        if (isset($update['callback_query']) && is_array($update['callback_query'])) {
            $this->handleCallback($update['callback_query']);
            return;
        }

        if (isset($update['message']) && is_array($update['message'])) {
            $this->handleMessage($update['message']);
        }
    }

    public function notifyVisitorMessage(SupportConversation $conversation, SupportMessage $message): void
    {
        $links = $this->activeLinksForClient($conversation->publicKey);
        if ($links === []) {
            return;
        }

        $text = $this->notificationText($conversation, $message);
        foreach ($links as $link) {
            $this->sendMessage(
                (string)$link->chat_id,
                $text,
                $this->conversationKeyboard((int)$conversation->id, $this->canCloseConversation($link)),
            );
        }
    }

    private function handleMessage(array $message): void
    {
        $chatId = (string)($message['chat']['id'] ?? '');
        $text = trim((string)($message['text'] ?? ''));
        if ($chatId === '' || $text === '') {
            return;
        }

        if (str_starts_with($text, '/start')) {
            $this->handleStart($chatId, $message, $text);
            return;
        }

        if ($text === '/dialogs') {
            $this->sendDialogs($chatId);
            return;
        }

        if (str_starts_with($text, '/reply')) {
            $this->handleReplyCommand($chatId, $text);
            return;
        }

        $link = $this->linkByChat($chatId);
        if ($link === null) {
            $this->sendMessage($chatId, 'Сначала привяжите Telegram в личном кабинете SiteWidget и отправьте /start CODE.');
            return;
        }

        if ((int)$link->pending_conversation_id <= 0) {
            $this->sendMessage($chatId, 'Выберите диалог кнопкой "Ответить" или используйте /reply ID текст.');
            return;
        }

        $this->replyToConversation($link, (int)$link->pending_conversation_id, $text);
    }

    private function handleStart(string $chatId, array $message, string $text): void
    {
        $parts = preg_split('/\s+/', $text) ?: [];
        $code = strtoupper(trim((string)($parts[1] ?? '')));
        if ($code === '') {
            $this->sendMessage($chatId, 'Откройте личный кабинет SiteWidget, получите код привязки Telegram и отправьте /start CODE.');
            return;
        }

        $codeRecord = SupportTelegramManagerCodeRecord::find()
            ->where(['code' => $code, 'used_at' => null])
            ->andWhere(['>', 'expires_at', new \yii\db\Expression('NOW()')])
            ->one();

        if (!$codeRecord instanceof SupportTelegramManagerCodeRecord) {
            $this->sendMessage($chatId, 'Код не найден или истек. Создайте новый код в личном кабинете.');
            return;
        }

        $from = $message['from'] ?? [];
        $link = SupportTelegramManagerLinkRecord::findOne(['chat_id' => $chatId]) ?? new SupportTelegramManagerLinkRecord();
        $link->user_id = (int)$codeRecord->user_id;
        $link->public_key = (int)$codeRecord->public_key;
        $link->chat_id = $chatId;
        $link->telegram_user_id = (string)($from['id'] ?? $chatId);
        $link->username = $from['username'] ?? null;
        $link->first_name = $from['first_name'] ?? null;
        $link->last_name = $from['last_name'] ?? null;
        $link->is_active = 1;
        $link->updated_at = new \yii\db\Expression('NOW()');
        $link->save(false);

        $codeRecord->used_at = new \yii\db\Expression('NOW()');
        $codeRecord->save(false, ['used_at']);

        $user = Users::findOne((int)$codeRecord->user_id);
        $this->sendMessage($chatId, 'Telegram подключен к менеджеру: ' . ($user?->name ?: 'SiteWidget') . ".\nКоманда /dialogs покажет открытые диалоги.");
    }

    private function handleCallback(array $callback): void
    {
        $chatId = (string)($callback['message']['chat']['id'] ?? '');
        $callbackId = (string)($callback['id'] ?? '');
        $data = (string)($callback['data'] ?? '');
        if ($chatId === '' || $data === '') {
            return;
        }

        [$action, $id] = array_pad(explode(':', $data, 2), 2, null);
        $conversationId = (int)$id;
        $link = $this->linkByChat($chatId);
        if ($link === null || $conversationId <= 0) {
            $this->answerCallback($callbackId, 'Telegram не привязан');
            return;
        }

        if ($action === 'reply') {
            $link->pending_conversation_id = $conversationId;
            $link->updated_at = new \yii\db\Expression('NOW()');
            $link->save(false, ['pending_conversation_id', 'updated_at']);
            $this->answerCallback($callbackId, 'Напишите ответ сообщением');
            $this->sendMessage($chatId, 'Пишите ответ для диалога #' . $conversationId . '.');
            return;
        }

        if ($action === 'close') {
            if (!$this->canCloseConversation($link)) {
                $this->answerCallback($callbackId, 'Закрывать диалоги может владелец аккаунта');
                return;
            }

            try {
                $this->operatorSupport->closeConversation((int)$link->public_key, $conversationId);
                $this->answerCallback($callbackId, 'Диалог закрыт');
                $this->sendMessage($chatId, 'Диалог #' . $conversationId . ' закрыт.');
            } catch (\Throwable $e) {
                Yii::error($e->getMessage(), 'telegram-manager-bot');
                $this->answerCallback($callbackId, 'Не удалось закрыть диалог');
                $this->sendMessage($chatId, 'Не удалось закрыть диалог #' . $conversationId . '. Проверьте его в панели управления.');
            }
        }
    }

    private function handleReplyCommand(string $chatId, string $text): void
    {
        $link = $this->linkByChat($chatId);
        if ($link === null) {
            $this->sendMessage($chatId, 'Telegram не привязан.');
            return;
        }

        if (!preg_match('/^\/reply\s+(\d+)\s+(.+)$/su', $text, $matches)) {
            $this->sendMessage($chatId, 'Формат: /reply ID текст ответа');
            return;
        }

        $this->replyToConversation($link, (int)$matches[1], trim($matches[2]));
    }

    private function replyToConversation(SupportTelegramManagerLinkRecord $link, int $conversationId, string $body): void
    {
        try {
            $this->operatorSupport->reply((int)$link->public_key, $conversationId, (int)$link->user_id, $body);
            $link->pending_conversation_id = null;
            $link->updated_at = new \yii\db\Expression('NOW()');
            $link->save(false, ['pending_conversation_id', 'updated_at']);
            $this->sendMessage((string)$link->chat_id, 'Ответ отправлен в диалог #' . $conversationId . '.');
        } catch (SupportLimitExceededException) {
            $this->sendMessage((string)$link->chat_id, 'Лимит ответов на сегодня исчерпан. Нужно обновить тариф или дождаться следующего дня.');
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'telegram-manager-bot');
            $this->sendMessage((string)$link->chat_id, 'Не удалось отправить ответ. Проверьте, что диалог открыт.');
        }
    }

    private function sendDialogs(string $chatId): void
    {
        $link = $this->linkByChat($chatId);
        if ($link === null) {
            $this->sendMessage($chatId, 'Telegram не привязан.');
            return;
        }

        $response = $this->operatorSupport->listConversations((int)$link->public_key, SupportConversation::STATUS_OPEN)->toArray();
        $conversations = array_slice($response['conversations'] ?? [], 0, 10);
        if ($conversations === []) {
            $this->sendMessage($chatId, 'Открытых диалогов нет.');
            return;
        }

        foreach ($conversations as $conversation) {
            $id = (int)($conversation['id'] ?? 0);
            $this->sendMessage($chatId, $this->conversationCardText($conversation), $this->conversationKeyboard($id, $this->canCloseConversation($link)));
        }
    }

    private function activeLinksForClient(int $publicKey): array
    {
        $ownerPublicKey = (int)(SupportProjectRecord::find()
            ->select('owner_public_key')
            ->where(['public_key' => $publicKey, 'enabled' => 1])
            ->scalar() ?: $publicKey);

        return SupportTelegramManagerLinkRecord::find()
            ->where(['public_key' => $ownerPublicKey, 'is_active' => 1])
            ->all();
    }

    private function linkByChat(string $chatId): ?SupportTelegramManagerLinkRecord
    {
        return SupportTelegramManagerLinkRecord::findOne(['chat_id' => $chatId, 'is_active' => 1]);
    }

    private function notificationText(SupportConversation $conversation, SupportMessage $message): string
    {
        $visitor = trim((string)($conversation->visitorName ?? ''))
            ?: trim((string)($conversation->visitorEmail ?? ''))
            ?: ('Диалог #' . $conversation->id);

        $lines = [
            'Новое сообщение SiteWidget',
            'Проект: ' . ($conversation->projectName ?: $conversation->publicKey),
            'Диалог: ' . $conversation->id,
            'Посетитель: ' . $visitor,
        ];

        if ($conversation->pageUrl !== null && trim($conversation->pageUrl) !== '') {
            $lines[] = 'Страница: ' . $conversation->pageUrl;
        }

        $lines[] = '';
        $lines[] = trim($message->body);
        $lines[] = '';
        $lines[] = $this->conversationHistoryText($conversation, 6);

        return implode("\n", $lines);
    }

    private function conversationCardText(array $conversation): string
    {
        $id = (int)($conversation['id'] ?? 0);
        $visitor = trim((string)($conversation['visitor_name'] ?? ''))
            ?: trim((string)($conversation['visitor_email'] ?? ''))
            ?: ('Диалог #' . $id);

        $lines = [
            $visitor,
            'Диалог: ' . $id,
        ];

        if (!empty($conversation['project_name']) || !empty($conversation['project_domain'])) {
            $lines[] = 'Проект: ' . trim((string)($conversation['project_name'] ?? '') . ' ' . (string)($conversation['project_domain'] ?? ''));
        }

        if (!empty($conversation['page_url'])) {
            $lines[] = 'Страница: ' . $conversation['page_url'];
        }

        $lines[] = '';
        $lines[] = $this->conversationHistoryText(
            new SupportConversation(
                id: $id,
                publicKey: (int)($conversation['public_key'] ?? 0),
                visitorId: (string)($conversation['visitor_id'] ?? ''),
            ),
            8
        );

        return implode("\n", array_filter($lines, static fn(string $line): bool => $line !== ''));
    }

    private function conversationHistoryText(SupportConversation $conversation, int $limit): string
    {
        if ($conversation->id === null || $conversation->publicKey <= 0) {
            return 'История сообщений недоступна.';
        }

        try {
            $messages = $this->operatorSupport
                ->listMessages($conversation->publicKey, $conversation->id)
                ->toArray()['messages'] ?? [];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'telegram-manager-bot');
            return 'История сообщений недоступна.';
        }

        $messages = array_slice($messages, -$limit);
        if ($messages === []) {
            return 'Сообщений пока нет.';
        }

        $lines = ['Последние сообщения:'];
        foreach ($messages as $message) {
            $sender = ($message['sender_type'] ?? '') === SupportMessage::SENDER_OPERATOR ? 'Менеджер' : 'Посетитель';
            $body = trim(strip_tags((string)($message['body'] ?? '')));
            $body = preg_replace('/\s+/u', ' ', $body) ?: '';
            if (mb_strlen($body) > 700) {
                $body = mb_substr($body, 0, 697) . '...';
            }

            $lines[] = $sender . ': ' . $body;
        }

        return implode("\n", $lines);
    }

    private function canCloseConversation(SupportTelegramManagerLinkRecord $link): bool
    {
        $user = Users::findOne((int)$link->user_id);

        return $user instanceof Users
            && (int)$user->id === (int)$link->public_key
            && (int)$user->public_key === (int)$link->public_key;
    }

    private function conversationKeyboard(int $conversationId, bool $canClose): array
    {
        $buttons = [
            ['text' => 'Ответить', 'callback_data' => 'reply:' . $conversationId],
        ];
        if ($canClose) {
            $buttons[] = ['text' => 'Закрыть', 'callback_data' => 'close:' . $conversationId];
        }

        return [
            'inline_keyboard' => [
                $buttons,
            ],
        ];
    }

    private function sendMessage(string $chatId, string $text, array $replyMarkup = []): void
    {
        $token = trim((string)($_ENV['TELEGRAM_MANAGER_BOT_TOKEN'] ?? ''));
        if ($token === '') {
            Yii::warning('TELEGRAM_MANAGER_BOT_TOKEN is empty', 'telegram-manager-bot');
            return;
        }

        $data = [
            'chat_id' => $chatId,
            'text' => $text,
            'disable_web_page_preview' => true,
        ];

        if ($replyMarkup !== []) {
            $data['reply_markup'] = json_encode($replyMarkup, JSON_UNESCAPED_UNICODE);
        }

        try {
            (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.telegram.org/bot' . $token . '/sendMessage')
                ->setData($data)
                ->send();
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'telegram-manager-bot');
        }
    }

    private function answerCallback(string $callbackId, string $text): void
    {
        $token = trim((string)($_ENV['TELEGRAM_MANAGER_BOT_TOKEN'] ?? ''));
        if ($token === '' || $callbackId === '') {
            return;
        }

        try {
            (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl('https://api.telegram.org/bot' . $token . '/answerCallbackQuery')
                ->setData([
                    'callback_query_id' => $callbackId,
                    'text' => $text,
                ])
                ->send();
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'telegram-manager-bot');
        }
    }
}
