<?php

$this->title = "Список ролей ";


use ruwmapps\yii2_uikit3\LinkPager; ?>

    <div class="uk-container uk-margin">
        <div uk-grid>
            <div class="uk-width-1-2">
                <button class="uk-button uk-button-primary new-role uk-margin-small-right"
                        type="button" uk-toggle="target: #modal-new">Создать
                </button>
                <div id="modal-new" uk-modal>
                    <div class="uk-modal-dialog uk-modal-body">
                        <?= $this->render('_formNewRole', compact('newrole')) ?>
                    </div>
                </div>
            </div>
        </div>
        <?php if(count($roles)){ ?>
            <div class="uk-grid-small uk-child-width-1-2@m uk-child-width-1-1@s" uk-grid>
                <?php foreach ($roles as $role) { ?>
                    <div>
                        <div class="uk-card uk-card-default uk-card-body">
                            <div class="uk-flex uk-flex-between uk-flex-top">
                                <div>
                                    <h4 class="uk-margin-remove"><?= $role->name ?></h4>
                                    <div class="uk-text-meta">ID роли на сайте: <?= $role->id_role_in_system ?></div>
                                </div>
                                <div class="uk-text-nowrap">
                                    <a href="#modal-edit<?= $role->id ?>" class="uk-icon-link uk-margin-small-right" uk-icon="file-edit" uk-toggle></a>
                                    <a href="#modal-delete<?= $role->id ?>" class="uk-icon-link" uk-icon="trash" uk-toggle></a>
                                </div>
                            </div>
                            <div id="modal-edit<?= $role->id ?>" uk-modal>
                                <div class="uk-modal-dialog uk-modal-body">
                                    <?= $this->render('_formUpdateRole', compact('role')) ?>
                                </div>
                            </div>
                            <div id="modal-delete<?= $role->id ?>" uk-modal>
                                <div class="uk-modal-dialog uk-modal-body">
                                    <?= $this->render('_formDeleteRole', compact('role')) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php }?>
    </div>
<?php
$js = <<<JS
        $('.new-role').click(function() {
$('#w0 .field-roles-name .uk-text-danger ').html('');
$('#w0 .field-roles-id_role_in_system .uk-text-danger ').html('');
})
JS;
$this->registerJs($js);
