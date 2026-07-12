<?php

use ruwmapps\yii2_uikit3\LinkPager;
use yii\helpers\Html;

/** @var $pages */
/** @var $users */
$users_label= new \app\Infrastructure\YiiActiveRecord\Users;

$this->title = "Список пользователей ";
?>

<div class="uk-container uk-margin">
    <div id="filters" uk-grid>
        <div class="uk-width-1-1 uk-text-right">
            <a href="/admin/clients/join" id="sg_but_new_course" class="uk-button uk-button-primary "
               type="button">Создать</a>
        </div>
    </div>
    <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
        <?php foreach ($users as $user): ?>
            <?php
            $delete = (!$user['status'])
                ? '<a href="#modal-delete-user' . $user['id'] . '" class="uk-icon-link" uk-icon="trash" uk-toggle uk-tooltip="Удалить"></a>
                   <div id="modal-delete-user' . $user['id'] . '" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            ' . $this->render('_formDeleteUser', compact('user')) . '
                        </div>
                   </div>'
                : '';
            $status = $user['status']
                ? '<span class="uk-margin-small-right uk-text-success" uk-icon="check"></span>Активен'
                : '<span class="uk-margin-small-right uk-text-danger" uk-icon="close"></span>Отключен';
            ?>
            <div>
                <div class="uk-card uk-card-default uk-card-body">
                    <div class="uk-flex uk-flex-between uk-flex-middle uk-margin-small-bottom">
                        <div>
                            <div class="uk-text-meta">PK <?= Html::encode((string)$user['public_key']) ?></div>
                            <h4 class="uk-margin-remove"><?= Html::encode((string)$user['firm']) ?></h4>
                        </div>
                        <div class="uk-text-nowrap">
                            <a href="/admin/clients/view?id=<?= Html::encode((string)$user['id']) ?>" class="uk-button uk-button-default uk-button-small uk-margin-small-right" uk-tooltip="Проекты и лимиты">
                                <span uk-icon="eye"></span> Просмотр
                            </a>
                            <a href="/admin/clients/update?id=<?= Html::encode((string)$user['id']) ?>" class="uk-icon-link uk-margin-small-right" uk-icon="file-edit" uk-tooltip="Редактировать"></a>
                            <?= $delete ?>
                        </div>
                    </div>
                    <div class="uk-text-lead uk-margin-small"><?= Html::encode((string)$user['name']) ?></div>
                    <div class="uk-text-meta"><?= $status ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php echo LinkPager::widget(['pagination' => $pages,]); ?>
</div>
