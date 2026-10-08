<?php

use yii\helpers\Html;
use app\Modules\Support\Presentation\Http\SupportPhoneLinkFormatter;

/** @var int $publicKey */
/** @var int $conversationId */
/** @var array<string, mixed> $conversation */
/** @var array<int, array<string, mixed>> $messages */

$this->title = 'Диалог #' . $conversationId;
?>

<div class="uk-container uk-margin">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
        <div>
            <h3 class="uk-margin-remove"><?= Html::encode($this->title) ?></h3>
            <div class="uk-text-meta">PK <?= Html::encode((string)$publicKey) ?> · только просмотр</div>
        </div>
        <?= Html::a('К диалогам проекта', '/admin/clients/dialogs?publicKey=' . $publicKey, ['class' => 'uk-button uk-button-default']) ?>
    </div>

    <?php
    $visitorLabel = (string)(
        ($conversation['visitor_name'] ?? '')
        ?: ($conversation['visitor_email'] ?? '')
        ?: ($conversation['visitor_id'] ?? '-')
    );
    ?>

    <div class="uk-card uk-card-default uk-card-body uk-margin">
        <div class="uk-text-meta">Посетитель</div>
        <div>
            <?= Html::encode($visitorLabel) ?>
        </div>
        <?php if (trim((string)($conversation['visitor_phone'] ?? '')) !== ''): ?>
            <div class="uk-text-meta uk-margin-small-top">Телефон</div>
            <div><?= SupportPhoneLinkFormatter::phone((string)$conversation['visitor_phone']) ?></div>
        <?php endif; ?>
        <div class="uk-text-meta uk-margin-small-top">Страница</div>
        <div><?= Html::encode((string)($conversation['page_url'] ?? '-')) ?></div>
    </div>

    <?php foreach ($messages as $message): ?>
        <div class="uk-card uk-card-default uk-card-body uk-margin-small">
            <div class="uk-text-meta">
                <?= Html::encode((string)($message['sender_type'] ?? $message['senderType'] ?? '-')) ?>
                ·
                <?= Html::encode((string)($message['created_at'] ?? $message['createdAt'] ?? '')) ?>
            </div>
            <div style="white-space: pre-wrap"><?= SupportPhoneLinkFormatter::message((string)($message['body'] ?? '')) ?></div>
        </div>
    <?php endforeach; ?>
</div>
