<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var array<int, array<string, mixed>> $conversations
 * @var string $status
 */

$this->title = 'Диалоги поддержки';
$waitingLabels = [
    'green' => 'Ждет ответа',
    'yellow' => 'Скоро минута',
    'red' => 'Долго ждет',
    'none' => 'Ответ не требуется',
];
$waitingColors = [
    'green' => '#2f9e44',
    'yellow' => '#f08c00',
    'red' => '#e03131',
    'none' => '#adb5bd',
];

$formatDateTime = static function (mixed $value): string {
    $raw = trim((string)$value);
    if ($raw === '') {
        return '-';
    }

    $timestamp = strtotime($raw);
    if ($timestamp === false) {
        return $raw;
    }

    return date('d.m.Y H:i', $timestamp);
};

$visitorLabel = static function (array $conversation): string {
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
?>

<div class="uk-container uk-position-relative">
    <h3>Диалоги поддержки</h3>

    <div id="support-realtime-notice" class="uk-alert-success" uk-alert hidden>
        <p>
            Есть новые сообщения.
            <?= Html::a('Обновить список', ['/manager/support/conversations', 'status' => $status], ['class' => 'uk-button uk-button-small uk-button-primary uk-margin-small-left']) ?>
        </p>
    </div>

    <div class="uk-margin">
        <?= Html::a('Открытые', ['/manager/support/conversations', 'status' => 'open'], [
            'class' => $status === 'open' ? 'uk-button uk-button-primary' : 'uk-button uk-button-default',
        ]) ?>
        <?= Html::a('Архив', ['/manager/support/conversations', 'status' => 'closed'], [
            'class' => $status === 'closed' ? 'uk-button uk-button-primary' : 'uk-button uk-button-default',
        ]) ?>
    </div>

    <?php if ($conversations === []): ?>
        <div class="uk-alert-primary" uk-alert>
            <p>Диалогов пока нет.</p>
        </div>
    <?php else: ?>
        <div class="sw-support-conversation-list">
            <?php foreach ($conversations as $conversation): ?>
                <?php
                $level = (string)($conversation['waiting_level'] ?? 'none');
                $entryPointTitle = trim((string)($conversation['entry_point_title'] ?? ''));
                $waitsForOperator = (bool)($conversation['waits_for_operator'] ?? false);
                ?>
                <article class="sw-support-conversation-row <?= $waitsForOperator ? 'sw-support-conversation-row--hot' : '' ?>">
                    <span
                        class="sw-support-wait-dot"
                        title="<?= Html::encode($waitingLabels[$level] ?? $waitingLabels['none']) ?>"
                        style="background:<?= Html::encode($waitingColors[$level] ?? $waitingColors['none']) ?>;"
                    ></span>

                    <div class="sw-support-conversation-row__visitor">
                        <strong><?= Html::encode($visitorLabel($conversation)) ?></strong>
                        <div class="uk-text-meta">
                            <?= Html::encode(trim((string)($conversation['project_name'] ?? '')) ?: 'Основной сайт') ?>
                            <?php if (trim((string)($conversation['project_domain'] ?? '')) !== ''): ?>
                                · <?= Html::encode((string)$conversation['project_domain']) ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="sw-support-conversation-row__topic">
                        <?= Html::encode($entryPointTitle !== '' ? $entryPointTitle : 'Обычное обращение') ?>
                        <?php if (trim((string)($conversation['visitor_phone'] ?? '')) !== ''): ?>
                            <div class="uk-text-meta"><?= Html::encode((string)$conversation['visitor_phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="sw-support-conversation-row__time">
                        <div><?= Html::encode($formatDateTime($conversation['last_message_at'] ?? null)) ?></div>
                        <?php if ($waitsForOperator): ?>
                            <div class="uk-text-meta">
                                <?= Html::encode((string)max(0, (int)floor(((int)$conversation['waiting_seconds']) / 60))) ?> мин.
                                <?= Html::encode((string)((int)$conversation['waiting_seconds'] % 60)) ?> сек.
                            </div>
                        <?php endif; ?>
                    </div>

                    <a class="uk-button uk-button-default uk-button-small" href="<?= Url::to(['/manager/support/conversation', 'id' => $conversation['id']]) ?>">
                        Открыть
                    </a>
                </article>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    var notice = document.getElementById('support-realtime-notice');

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
                    if (payload.type === 'support.message' && notice) {
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
