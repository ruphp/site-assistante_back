<?php

use app\Modules\Support\Application\Dto\SupportUsageOwnerReport;
use yii\helpers\Html;

/** @var SupportUsageOwnerReport[] $reports */

$this->title = 'Лимиты клиентов';

$percent = static function (int $used, int $limit): int {
    return $limit <= 0 ? 0 : min(100, (int)round($used / $limit * 100));
};

$progress = static function (int $used, int $limit) use ($percent): string {
    return '<progress class="uk-progress uk-margin-small-top" value="' . $percent($used, $limit) . '" max="100"></progress>';
};

?>

<div class="uk-container uk-margin">
    <h3>Лимиты клиентов</h3>

    <table class="uk-table uk-table-divider uk-table-middle">
        <thead>
        <tr>
            <th>Клиент</th>
            <th>Тариф</th>
            <th>Ответы сегодня</th>
            <th>Проекты</th>
            <th>Операторы</th>
            <th>Диалоги за месяц</th>
            <th>Сообщения за месяц</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reports as $report): ?>
            <tr>
                <td>
                    <strong><?= Html::encode($report->firm !== '' ? $report->firm : $report->ownerName) ?></strong>
                    <div class="uk-text-meta">PK <?= $report->ownerPublicKey ?></div>
                    <div class="uk-text-meta"><?= Html::encode($report->ownerEmail) ?></div>
                </td>
                <td><?= Html::encode($report->planLabel) ?></td>
                <td>
                    <?= $report->operatorRepliesToday ?> / <?= $report->operatorRepliesPerDayLimit ?>
                    <?= $progress($report->operatorRepliesToday, $report->operatorRepliesPerDayLimit) ?>
                </td>
                <td>
                    <?= $report->projectsCount ?> / <?= $report->projectsLimit ?>
                    <?= $progress($report->projectsCount, $report->projectsLimit) ?>
                </td>
                <td>
                    <?= $report->operatorsCount ?> / <?= $report->operatorsLimit ?>
                    <?= $progress($report->operatorsCount, $report->operatorsLimit) ?>
                </td>
                <td>
                    <?= $report->conversationsMonth ?> / <?= $report->conversationsMonthLimit ?>
                    <?= $progress($report->conversationsMonth, $report->conversationsMonthLimit) ?>
                </td>
                <td>
                    <?= $report->messagesMonth ?> / <?= $report->messagesMonthLimit ?>
                    <?= $progress($report->messagesMonth, $report->messagesMonthLimit) ?>
                </td>
                <td class="uk-text-nowrap">
                    <?= Html::a('Сбросить ответы за сегодня', [
                        '/admin/clients/limits/reset-daily-replies',
                        'publicKey' => $report->ownerPublicKey,
                    ], [
                        'class' => 'uk-button uk-button-default uk-button-small',
                        'data-method' => 'post',
                        'data-confirm' => 'Сбросить дневной лимит ответов для клиента?',
                    ]) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
