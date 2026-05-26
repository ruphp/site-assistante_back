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
            <table class="uk-table uk-table-divider">
                <thead>
                <tr>
                    <th class="uk-table-expand">Название</th>
                    <th class="uk-table-expand">Идентификатор роли на Вашем сайте</th>
                    <th class="uk-table-shrink"></th>
                    <th class="uk-table-shrink"></th>
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($roles as $role) {?>
                    <tr>
                        <td><?= $role->name ?></td>
                        <td><?= $role->id_role_in_system ?></td>

                        <td class="uk-padding-remove-horizontal"><a
                                href="#modal-edit<?= $role->id ?>"
                                class="uk-icon-link uk-margin-small-right"
                                uk-icon="file-edit" uk-toggle></a></td>
                        <div id="modal-edit<?= $role->id ?>" uk-modal>
                            <div class="uk-modal-dialog uk-modal-body">
                                <?= $this->render('_formUpdateRole', compact('role')) ?>
                            </div>
                        </div>


                        <td class="uk-padding-remove-horizontal"><a
                                href="#modal-delete<?= $role->id ?>"
                                class="uk-icon-link" uk-icon="trash"
                                uk-toggle></a></td>
                        <div id="modal-delete<?= $role->id ?>" uk-modal>
                            <div class="uk-modal-dialog uk-modal-body">
                                <?= $this->render('_formDeleteRole', compact('role')) ?>
                            </div>
                        </div>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
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
