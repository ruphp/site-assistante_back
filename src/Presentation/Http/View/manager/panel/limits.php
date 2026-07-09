<?php

use app\Modules\Support\Application\Dto\SupportUsageOwnerReport;
use yii\helpers\Html;

/** @var SupportUsageOwnerReport $report */

$this->title = 'Лимиты и статистика';

$percent = static function (int $used, int $limit): int {
    return $limit <= 0 ? 0 : min(100, (int)round($used / $limit * 100));
};

$progress = static function (int $used, int $limit) use ($percent): string {
    return '<progress class="uk-progress uk-margin-small-top" value="' . $percent($used, $limit) . '" max="100"></progress>';
};

?>

<div class="uk-container uk-margin">
    <h3>Лимиты и статистика</h3>

    <div class="uk-grid-small uk-child-width-1-3@m uk-child-width-1-2@s" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Тариф</div>
                <h4 class="uk-margin-small"><?= Html::encode($report->planLabel) ?></h4>
                <div class="uk-text-meta">Проекты: <?= $report->projectsCount ?> / <?= $report->projectsLimit ?></div>
                <div class="uk-text-meta">Операторы: <?= $report->operatorsCount ?> / <?= $report->operatorsLimit ?></div>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Ответы операторов сегодня</div>
                <h4 class="uk-margin-small"><?= $report->operatorRepliesToday ?> / <?= $report->operatorRepliesPerDayLimit ?></h4>
                <?= $progress($report->operatorRepliesToday, $report->operatorRepliesPerDayLimit) ?>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Сообщения за месяц</div>
                <h4 class="uk-margin-small"><?= $report->messagesMonth ?> / <?= $report->messagesMonthLimit ?></h4>
                <?= $progress($report->messagesMonth, $report->messagesMonthLimit) ?>
            </div>
        </div>
    </div>

    <h4 class="uk-margin-large-top">Проекты</h4>
    <table class="uk-table uk-table-divider uk-table-middle">
        <thead>
        <tr>
            <th>Проект</th>
            <th>Public key</th>
            <th>Тариф</th>
            <th>Ответы сегодня</th>
            <th>Диалоги за месяц</th>
            <th>Сообщения за месяц</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($report->projects as $project): ?>
            <tr>
                <td>
                    <strong><?= Html::encode($project->projectName) ?></strong>
                    <?php if ($project->domain !== ''): ?>
                        <div class="uk-text-meta"><?= Html::encode($project->domain) ?></div>
                    <?php endif; ?>
                </td>
                <td><?= $project->publicKey ?></td>
                <td><?= Html::encode($project->planLabel) ?></td>
                <td>
                    <?= $project->operatorRepliesToday ?> / <?= $project->operatorRepliesPerDayLimit ?>
                    <?= $progress($project->operatorRepliesToday, $project->operatorRepliesPerDayLimit) ?>
                </td>
                <td>
                    <?= $project->conversationsMonth ?> / <?= $project->conversationsMonthLimit ?>
                    <?= $progress($project->conversationsMonth, $project->conversationsMonthLimit) ?>
                </td>
                <td>
                    <?= $project->messagesMonth ?> / <?= $project->messagesMonthLimit ?>
                    <?= $progress($project->messagesMonth, $project->messagesMonthLimit) ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
