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
                <th>Статус</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($conversations as $conversation): ?>
                <tr>
                    <td><?= Html::encode((string)$conversation['id']) ?></td>
                    <td><?= Html::encode((string)$conversation['visitor_id']) ?></td>
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
