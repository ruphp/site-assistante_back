<?php

use yii\helpers\Html;

/**
 * @var int $conversationId
 * @var array<string, mixed>|null $conversation
 * @var array<int, array<string, mixed>> $messages
 */

$this->title = 'Диалог поддержки';
?>

<div class="uk-container uk-position-relative">
    <p><?= Html::a('← К списку диалогов', ['/manager/support/conversations']) ?></p>
    <h3>Диалог #<?= Html::encode((string)$conversationId) ?></h3>
    <?php if ($conversation !== null): ?>
        <div class="uk-alert-primary" uk-alert>
            <p>
                Посетитель:
                <?= Html::encode((string)($conversation['visitor_email'] ?: $conversation['visitor_id'])) ?>
            </p>
            <?php if ($conversation['page_url']): ?>
                <p>
                    Страница:
                    <a href="<?= Html::encode((string)$conversation['page_url']) ?>" target="_blank" rel="noopener noreferrer">
                        <?= Html::encode((string)$conversation['page_url']) ?>
                    </a>
                </p>
            <?php endif; ?>
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
</div>
