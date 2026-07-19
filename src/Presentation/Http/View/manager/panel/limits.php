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
    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($report->projects as $project): ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-top uk-margin-small-bottom">
                        <div>
                            <strong><?= Html::encode($project->projectName) ?></strong>
                            <?php if ($project->domain !== ''): ?>
                                <div class="uk-text-meta"><?= Html::encode($project->domain) ?></div>
                            <?php endif; ?>
                        </div>
                        <div class="uk-text-meta">PK <?= Html::encode((string)$project->publicKey) ?></div>
                    </div>
                    <div class="uk-margin-small-bottom">
                        <span class="uk-label uk-label-primary"><?= Html::encode($project->planLabel) ?></span>
                    </div>
                    <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
                        <div>
                            <div class="uk-text-meta">Ответы сегодня</div>
                            <div><?= $project->operatorRepliesToday ?> / <?= $project->operatorRepliesPerDayLimit ?></div>
                            <?= $progress($project->operatorRepliesToday, $project->operatorRepliesPerDayLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Диалоги за месяц</div>
                            <div><?= $project->conversationsMonth ?> / <?= $project->conversationsMonthLimit ?></div>
                            <?= $progress($project->conversationsMonth, $project->conversationsMonthLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Сообщения за месяц</div>
                            <div><?= $project->messagesMonth ?> / <?= $project->messagesMonthLimit ?></div>
                            <?= $progress($project->messagesMonth, $project->messagesMonthLimit) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
