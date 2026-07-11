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

    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($reports as $report): ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-top uk-margin-small-bottom">
                        <div>
                            <strong><?= Html::encode($report->firm !== '' ? $report->firm : $report->ownerName) ?></strong>
                            <div class="uk-text-meta"><?= Html::encode($report->ownerEmail) ?></div>
                            <div class="uk-text-meta">PK <?= Html::encode((string)$report->ownerPublicKey) ?></div>
                        </div>
                        <div class="uk-text-nowrap">
                            <?= Html::a('Сбросить ответы за сегодня', [
                                '/admin/clients/limits/reset-daily-replies',
                                'publicKey' => $report->ownerPublicKey,
                            ], [
                                'class' => 'uk-button uk-button-default uk-button-small',
                                'data-method' => 'post',
                                'data-confirm' => 'Сбросить дневной лимит ответов для клиента?',
                            ]) ?>
                        </div>
                    </div>

                    <div class="uk-margin-small-bottom">
                        <span class="uk-label uk-label-primary"><?= Html::encode($report->planLabel) ?></span>
                    </div>

                    <div class="uk-grid-small uk-child-width-1-2@s" uk-grid>
                        <div>
                            <div class="uk-text-meta">Ответы сегодня</div>
                            <div><?= $report->operatorRepliesToday ?> / <?= $report->operatorRepliesPerDayLimit ?></div>
                            <?= $progress($report->operatorRepliesToday, $report->operatorRepliesPerDayLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Проекты</div>
                            <div><?= $report->projectsCount ?> / <?= $report->projectsLimit ?></div>
                            <?= $progress($report->projectsCount, $report->projectsLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Операторы</div>
                            <div><?= $report->operatorsCount ?> / <?= $report->operatorsLimit ?></div>
                            <?= $progress($report->operatorsCount, $report->operatorsLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Диалоги за месяц</div>
                            <div><?= $report->conversationsMonth ?> / <?= $report->conversationsMonthLimit ?></div>
                            <?= $progress($report->conversationsMonth, $report->conversationsMonthLimit) ?>
                        </div>
                        <div>
                            <div class="uk-text-meta">Сообщения за месяц</div>
                            <div><?= $report->messagesMonth ?> / <?= $report->messagesMonthLimit ?></div>
                            <?= $progress($report->messagesMonth, $report->messagesMonthLimit) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
