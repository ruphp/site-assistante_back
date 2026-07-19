<?php

use app\Modules\Support\Application\Dto\SupportUsageOwnerReport;
use ruwmapps\yii2_uikit3\LinkPager;
use yii\helpers\Html;

/** @var $pages */
/** @var SupportUsageOwnerReport[] $clients */

$this->title = 'Клиенты';

$limitText = static function (int $used, int $limit): string {
    return $limit > 0 ? $used . ' / ' . $limit : (string)$used;
};
?>

<div class="uk-container uk-margin">
    <div id="filters" uk-grid>
        <div class="uk-width-1-1 uk-text-right">
            <a href="/admin/clients/join" id="sg_but_new_course" class="uk-button uk-button-primary "
               type="button">Создать</a>
        </div>
    </div>
    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($clients as $client): ?>
            <?php
            $title = $client->firm !== '' ? $client->firm : $client->ownerName;
            ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-small-bottom">
                        <div>
                            <div class="uk-text-meta">PK <?= Html::encode((string)$client->ownerPublicKey) ?></div>
                            <h4 class="uk-margin-remove"><?= Html::encode($title) ?></h4>
                        </div>
                        <div class="uk-text-nowrap">
                            <a href="/admin/clients/view?id=<?= Html::encode((string)$client->ownerUserId) ?>" class="uk-button uk-button-default uk-button-small uk-margin-small-right" uk-tooltip="Проекты и лимиты">
                                <span uk-icon="eye"></span> Просмотр
                            </a>
                            <a href="/admin/clients/update?id=<?= Html::encode((string)$client->ownerUserId) ?>" class="uk-icon-link uk-margin-small-right" uk-icon="file-edit" uk-tooltip="Редактировать"></a>
                        </div>
                    </div>
                    <div class="uk-text-lead uk-margin-small"><?= Html::encode($client->ownerName) ?></div>
                    <div class="uk-text-meta uk-margin-small-bottom"><?= Html::encode($client->ownerEmail) ?></div>
                    <div class="uk-grid-small uk-child-width-1-3@s" uk-grid>
                        <div>
                            <div class="uk-text-meta">Тариф</div>
                            <strong><?= Html::encode($client->planLabel) ?></strong>
                        </div>
                        <div>
                            <div class="uk-text-meta">Проекты</div>
                            <strong><?= Html::encode($limitText($client->projectsCount, $client->projectsLimit)) ?></strong>
                        </div>
                        <div>
                            <div class="uk-text-meta">Менеджеры</div>
                            <strong><?= Html::encode($limitText($client->operatorsCount, $client->operatorsLimit)) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php echo LinkPager::widget(['pagination' => $pages,]); ?>
</div>
