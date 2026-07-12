<?php

use yii\helpers\Html;

/** @var int $publicKey */
/** @var string $status */
/** @var array<int, array<string, mixed>> $conversations */

$this->title = 'Диалоги проекта PK ' . $publicKey;
?>

<div class="uk-container uk-margin">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
        <div>
            <h3 class="uk-margin-remove"><?= Html::encode($this->title) ?></h3>
            <div class="uk-text-meta">Только просмотр для супер-админа</div>
        </div>
        <?= Html::a('К клиентам', '/admin/clients', ['class' => 'uk-button uk-button-default']) ?>
    </div>

    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($conversations as $conversation): ?>
            <?php
            $visitorLabel = (string)(
                ($conversation['visitor_name'] ?? '')
                ?: ($conversation['visitor_email'] ?? '')
                ?: ($conversation['visitor_id'] ?? '-')
            );
            ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-top">
                        <div>
                            <div class="uk-text-meta">Диалог #<?= Html::encode((string)$conversation['id']) ?></div>
                            <h4 class="uk-margin-remove"><?= Html::encode($visitorLabel) ?></h4>
                        </div>
                        <span class="uk-label"><?= Html::encode((string)$conversation['status']) ?></span>
                    </div>
                    <div class="uk-text-meta uk-margin-small-top"><?= Html::encode((string)($conversation['page_url'] ?? '-')) ?></div>
                    <div class="uk-margin-small-top">
                        Приоритет <?= Html::encode((string)($conversation['priority'] ?? 0)) ?> ·
                        <?= !empty($conversation['waits_for_operator']) ? 'ждёт оператора' : 'ответ не требуется' ?>
                    </div>
                    <?= Html::a('Открыть переписку', '/admin/clients/dialog?publicKey=' . $publicKey . '&conversationId=' . (int)$conversation['id'], [
                        'class' => 'uk-button uk-button-primary uk-button-small uk-margin-small-top',
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <?php if ($conversations === []): ?>
        <div class="uk-alert-primary" uk-alert>Открытых диалогов по проекту пока нет.</div>
    <?php endif; ?>
</div>
