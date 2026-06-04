<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var array<int, array<string, mixed>> $conversations
 * @var string $status
 */

$this->title = 'Диалоги поддержки';
?>

<div class="uk-container uk-position-relative">
    <h3>Диалоги поддержки</h3>

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
