<?php

use yii\helpers\Html;

/**
 * @var int $conversationId
 * @var array<string, mixed>|null $conversation
 * @var array<int, array<string, mixed>> $messages
 */

$this->title = 'Диалог поддержки';

$visitorLabel = static function (?array $conversation): string {
    if ($conversation === null) {
        return '';
    }

    $name = trim((string)($conversation['visitor_name'] ?? ''));
    if ($name !== '') {
        return $name;
    }

    $email = trim((string)($conversation['visitor_email'] ?? ''));
    if ($email !== '') {
        return $email;
    }

    $visitorId = trim((string)($conversation['visitor_id'] ?? ''));
    if (str_starts_with($visitorId, 'email:')) {
        return substr($visitorId, 6);
    }

    return $visitorId;
};

$isArchived = ($conversation['status'] ?? null) === 'closed';
?>

<div class="uk-container uk-position-relative">
    <p><?= Html::a('← К списку диалогов', ['/manager/support/conversations']) ?></p>
    <h3>Диалог #<?= Html::encode((string)$conversationId) ?></h3>
    <div id="support-realtime-notice" class="uk-alert-success" uk-alert hidden>
        <p>
            В этом диалоге есть новые сообщения.
            <?= Html::a('Обновить диалог', ['/manager/support/conversation', 'id' => $conversationId], ['class' => 'uk-button uk-button-small uk-button-primary uk-margin-small-left']) ?>
        </p>
    </div>
    <?php if ($conversation !== null): ?>
        <div class="uk-alert-primary" uk-alert>
            <p>
                Посетитель:
                <strong style="display:inline-block;max-width:100%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;vertical-align:bottom;"><?= Html::encode($visitorLabel($conversation)) ?></strong>
            </p>
            <?php if ($conversation['page_url']): ?>
                <p>
                    Страница:
                    <a href="<?= Html::encode((string)$conversation['page_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <?= Html::encode((string)$conversation['page_url']) ?>
                    </a>
                </p>
            <?php endif; ?>
            <p>
                Кнопка:
                <strong><?= Html::encode(trim((string)($conversation['entry_point_title'] ?? '')) ?: '—') ?></strong>
            </p>
            <p class="uk-text-meta">
                Приоритет: <?= Html::encode((string)($conversation['priority'] ?? 0)) ?>
            </p>
        </div>
    <?php endif; ?>

    <div class="uk-margin">
        <?php if ($messages === []): ?>
            <div class="uk-alert-primary" uk-alert>
                <p>Сообщений пока нет.</p>
            </div>
        <?php else: ?>
            <?php foreach ($messages as $message): ?>
                <div class="uk-card uk-card-small uk-card-default uk-margin-small">
                    <div class="uk-card-body">
                        <div class="uk-text-meta">
                            <?= Html::encode((string)$message['sender_type']) ?>
                            <?php if ($message['created_at']): ?>
                                · <?= Html::encode((string)$message['created_at']) ?>
                            <?php endif; ?>
                        </div>
                        <div style="white-space: pre-wrap"><?= Html::encode((string)$message['body']) ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php if ($isArchived): ?>
        <div class="uk-alert-primary" uk-alert>
            <p>Диалог находится в архиве.</p>
        </div>
    <?php endif; ?>

    <div class="uk-margin uk-flex uk-flex-gap-small">
        <?php if (!$isArchived): ?>
            <?= Html::beginForm(['/manager/support/conversation-close'], 'post', ['style' => 'display:inline']) ?>
                <?= Html::hiddenInput('id', (string)$conversationId) ?>
                <?= Html::submitButton('В архив', [
                    'class' => 'uk-button uk-button-default',
                    'onclick' => "return confirm('Отправить диалог в архив?');",
                ]) ?>
            <?= Html::endForm() ?>
        <?php endif; ?>

        <?= Html::beginForm(['/manager/support/conversation-delete'], 'post', ['style' => 'display:inline']) ?>
            <?= Html::hiddenInput('id', (string)$conversationId) ?>
            <?= Html::submitButton('Удалить', [
                'class' => 'uk-button uk-button-danger',
                'onclick' => "return confirm('Удалить диалог и сообщения?');",
            ]) ?>
        <?= Html::endForm() ?>
    </div>

    <?php if (!$isArchived): ?>
        <?= Html::beginForm('/manager/support/reply', 'post', ['class' => 'uk-form-stacked']) ?>
            <?= Html::hiddenInput('conversation_id', (string)$conversationId) ?>
            <div class="uk-margin">
                <?= Html::label('Ответ оператора', 'support-reply-body', ['class' => 'uk-form-label']) ?>
                <?= Html::textarea('body', '', [
                    'id' => 'support-reply-body',
                    'class' => 'uk-textarea',
                    'rows' => 5,
                ]) ?>
            </div>
            <?= Html::submitButton('Отправить', ['class' => 'uk-button uk-button-primary']) ?>
        <?= Html::endForm() ?>
    <?php endif; ?>
</div>

<?php
$this->registerJs(<<<JS
(function () {
    var notice = document.getElementById('support-realtime-notice');
    var currentConversationId = {$conversationId};

    function wsUrlFromResponse(data) {
        if (data.wsUrl) {
            return data.wsUrl;
        }

        var protocol = window.location.protocol === 'https:' ? 'wss:' : 'ws:';
        return protocol + '//' + window.location.hostname + ':8081';
    }

    fetch('/manager/support/ws-token')
        .then(function (response) { return response.json(); })
        .then(function (data) {
            var socket = new WebSocket(wsUrlFromResponse(data));

            socket.addEventListener('open', function () {
                socket.send(JSON.stringify({
                    type: 'subscribeManager',
                    token: data.token
                }));
            });

            socket.addEventListener('message', function (event) {
                try {
                    var payload = JSON.parse(event.data);
                    if (
                        payload.type === 'support.message' &&
                        Number(payload.conversationId) === currentConversationId &&
                        notice
                    ) {
                        notice.hidden = false;
                    }
                } catch (e) {
                }
            });
        })
        .catch(function () {
        });
})();
JS, \yii\web\View::POS_READY);
?>
