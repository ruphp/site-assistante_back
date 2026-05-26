<?php

use ruwmapps\yii2_uikit3\LinkPager;

/** @var $pages */
/** @var $users */
$users_label= new \app\models\Users;

$this->title = "Список пользователей ";
?>

<div class="uk-container uk-margin">
    <div id="filters" uk-grid>
        <div class="uk-width-1-1 uk-text-right">
            <a href="/admin/clients/join" id="sg_but_new_course" class="uk-button uk-button-primary "
               type="button">Создать</a>
        </div>
    </div>
    <table class="uk-table uk-table-striped">
        <thead>
        <tr>
            <th class="uk-table-expand"><?= $users_label->attributeLabels()['public_key'] ?></th>
            <th class="uk-table-expand"><?= $users_label->attributeLabels()['firm'] ?></th>
            <th class="uk-table-expand"><?= $users_label->attributeLabels()['name'] ?></th>
            <th class="uk-table-expand">Доступность контента</th>
            <th class="uk-table-shrink"></th>
            <th class="uk-table-shrink"></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $table= '';
        foreach ($users as $user) {

            $delete=(!$user['status'])
                ? '<a href="#modal-delete-user' . $user['id'] . '" class="uk-icon-link" uk-icon="trash" uk-toggle uk-tooltip="Удалить"></a>                    
                   <div id="modal-delete-user' . $user['id'] . '" uk-modal>
                        <div class="uk-modal-dialog uk-modal-body">
                            ' . $this->render('_formDeleteUser', compact('user')) . '
                        </div>
                   </div>'
            : '';


            $auth = Yii::$app->authManager;
            $status = $user['status']?'<span class="uk-margin-small-right uk-text-success" uk-icon="check"></span>':'<span class="uk-margin-small-right uk-text-danger" uk-icon="close"></span>';
            $table .= '<tr>
                            <td>' . $user['public_key'] . '</td>
                            <td>' . $user['firm'] . '</td>
                            <td>' . $user['name'] . '</td>
                            <td>' . $status . '</td>
                            <td class="uk-padding-remove-horizontal">
                                <a href="/admin/clients/update?id=' . $user['id'] . '" class="uk-icon-link" uk-icon="file-edit" uk-toggle uk-tooltip="Редактировать"></a>
                            </td>
                            <td class="uk-padding-remove-horizontal">'.$delete.'</td>
                    </tr>';
        }
        echo  $table;
        ?>
        </tbody>
        <!-- -->
    </table>
    <?php echo LinkPager::widget(['pagination' => $pages,]); ?>
</div>