<?php

use app\Modules\Support\Application\Dto\SupportUsageOwnerReport;
use app\Modules\Support\Application\Dto\SupportUsageProjectReport;
use yii\helpers\Html;

/** @var object|null $user */
/** @var SupportUsageOwnerReport $report */

$this->title = 'Клиент: ' . ($report->firm !== '' ? $report->firm : $report->ownerName);

$limitText = static function (int $used, int $limit): string {
    return $limit > 0 ? $used . ' / ' . $limit : (string)$used;
};
$bytesText = static function (int $bytes): string {
    if ($bytes >= 1024 * 1024 * 1024) {
        return round($bytes / 1024 / 1024 / 1024, 1) . ' ГБ';
    }
    if ($bytes >= 1024 * 1024) {
        return round($bytes / 1024 / 1024, 1) . ' МБ';
    }

    return round($bytes / 1024, 1) . ' КБ';
};
?>

<div class="uk-container uk-margin">
    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-bottom">
        <div>
            <h3 class="uk-margin-remove"><?= Html::encode($this->title) ?></h3>
            <div class="uk-text-meta">
                PK <?= Html::encode((string)$report->ownerPublicKey) ?> ·
                <?= Html::encode($report->ownerEmail) ?> ·
                тариф <?= Html::encode($report->planLabel) ?>
            </div>
        </div>
        <div class="uk-text-nowrap">
            <?= Html::a('Редактировать', '/admin/clients/update?id=' . (int)($user?->id ?? 0), ['class' => 'uk-button uk-button-default']) ?>
            <?= Html::a('К списку', '/admin/clients', ['class' => 'uk-button uk-button-primary']) ?>
        </div>
    </div>

    <div class="uk-grid-small uk-child-width-1-4@m uk-child-width-1-2@s" uk-grid>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Ответы сегодня</div>
                <div class="uk-text-lead"><?= Html::encode($limitText($report->operatorRepliesToday, $report->operatorRepliesPerDayLimit)) ?></div>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Диалоги за месяц</div>
                <div class="uk-text-lead"><?= Html::encode($limitText($report->conversationsMonth, $report->conversationsMonthLimit)) ?></div>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Сообщения за месяц</div>
                <div class="uk-text-lead"><?= Html::encode($limitText($report->messagesMonth, $report->messagesMonthLimit)) ?></div>
            </div>
        </div>
        <div>
            <div class="uk-card uk-card-default uk-card-body">
                <div class="uk-text-meta">Проекты / менеджеры</div>
                <div class="uk-text-lead">
                    <?= Html::encode($limitText($report->projectsCount, $report->projectsLimit)) ?>
                    ·
                    <?= Html::encode($limitText($report->operatorsCount, $report->operatorsLimit)) ?>
                </div>
            </div>
        </div>
    </div>

    <h4 class="uk-margin-large-top">Проекты клиента</h4>
    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($report->projects as $project): ?>
            <?php /** @var SupportUsageProjectReport $project */ ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-top">
                        <div>
                            <div class="uk-text-meta">PK <?= Html::encode((string)$project->publicKey) ?></div>
                            <h4 class="uk-margin-remove"><?= Html::encode($project->projectName) ?></h4>
                            <div class="uk-text-meta"><?= Html::encode($project->domain) ?></div>
                        </div>
                    </div>
                    <dl class="uk-description-list uk-description-list-divider uk-margin-small-top">
                        <dt>Ответы сегодня</dt>
                        <dd><?= Html::encode($limitText($project->operatorRepliesToday, $project->operatorRepliesPerDayLimit)) ?></dd>
                        <dt>Диалоги за месяц</dt>
                        <dd><?= Html::encode($limitText($project->conversationsMonth, $project->conversationsMonthLimit)) ?></dd>
                        <dt>Сообщения за месяц</dt>
                        <dd><?= Html::encode($limitText($project->messagesMonth, $project->messagesMonthLimit)) ?></dd>
                        <dt>Место инструкций</dt>
                        <dd>
                            <?= Html::encode($bytesText($project->instructionStorageBytes)) ?>
                            из
                            <?= Html::encode($bytesText($project->instructionStorageLimitBytes)) ?>
                        </dd>
                    </dl>
                    <?= Html::a('Смотреть диалоги', '/admin/clients/dialogs?publicKey=' . $project->publicKey, [
                        'class' => 'uk-button uk-button-default uk-button-small',
                    ]) ?>
                    <?= Html::a('Инструкции', '/admin/instructions?publicKey=' . $project->publicKey, [
                        'class' => 'uk-button uk-button-default uk-button-small',
                    ]) ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
