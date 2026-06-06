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
        <?= Html::a('Закрытые', ['/manager/support/conversations', 'status' => 'closed'], [
            'class' => $status === 'closed' ? 'uk-button uk-button-primary' : 'uk-button uk-button-default',
        ]) ?>
    </div>

    <?php if ($conversations === []): ?>
        <div class="uk-alert-primary" uk-alert>
            <p>Диалогов пока нет.</p>
        </div>
    <?php else: ?>
        <table class="uk-table uk-table-divider uk-table-hover">
            <thead>
            <tr>
                <th>ID</th>
                <th>Посетитель</th>
                <th>Страница</th>
                <th>Приоритет</th>
                <th>Ожидание</th>
                <th>Статус</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($conversations as $conversation): ?>
                <tr>
                    <td><?= Html::encode((string)$conversation['id']) ?></td>
                    <td>
                        <?= Html::encode((string)($conversation['visitor_email'] ?: $conversation['visitor_id'])) ?>
                        <?php if ($conversation['visitor_email']): ?>
                            <div class="uk-text-meta"><?= Html::encode((string)$conversation['visitor_id']) ?></div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($conversation['page_url']): ?>
                            <a href="<?= Html::encode((string)$conversation['page_url']) ?>" target="_blank" rel="noopener noreferrer">
                                <?= Html::encode((string)parse_url((string)$conversation['page_url'], PHP_URL_PATH) ?: $conversation['page_url']) ?>
                            </a>
                        <?php else: ?>
                            <span class="uk-text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= Html::encode((string)($conversation['priority'] ?? 0)) ?></td>
                    <td>
                        <?php $level = (string)($conversation['waiting_level'] ?? 'none'); ?>
                        <span
                            title="<?= Html::encode($waitingLabels[$level] ?? $waitingLabels['none']) ?>"
                            style="display:inline-block;width:10px;height:10px;border-radius:50%;background:<?= Html::encode($waitingColors[$level] ?? $waitingColors['none']) ?>;margin-right:6px;"
                        ></span>
                        <?php if ($conversation['waits_for_operator']): ?>
                            <?= Html::encode((string)max(0, (int)floor(((int)$conversation['waiting_seconds']) / 60))) ?> мин.
                            <?= Html::encode((string)((int)$conversation['waiting_seconds'] % 60)) ?> сек.
                        <?php else: ?>
                            <span class="uk-text-muted">-</span>
                        <?php endif; ?>
                    </td>
                    <td><?= Html::encode((string)$conversation['status']) ?></td>
                    <td>
                        <a href="<?= Url::to(['/manager/support/conversation', 'id' => $conversation['id']]) ?>">
                            Открыть
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
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
