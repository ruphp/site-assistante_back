<?php

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var array $user
 * @var array $user_modules
 * @var array $name_modules
 */

$this->title = 'Изменение данных клиента';
?>
<div class="uk-container uk-container-xsmall">
    <div>
        <div class="uk-card uk-card-large uk-card-default uk-card-body">
            <h2 class="bd-title">Изменение данных клиента</h2>
            <?php



            app\assets\AppAsset::register($this);
            $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']);
            $user['change_password'] = 0;
            $user['modules'] = $user_modules;
            ?>
            <?= $form->field($user, 'firm') ?>
            <?= $form->field($user, 'name') ?>
            <?php
            if($_ENV['TYPE_AUTH'] == 'RSAA' ){
                echo $form->field($user, 'email')->hiddenInput()->label('');
            }else{
                echo $form->field($user, 'email')->label('Адрес электронной почты');
            }

            ?>
            <?= $form->field($user, 'email')->hiddenInput()->label('')?>


            <?php
                        if($_ENV['TYPE_DEPLOYED'] == 'MIRS' ){
                            echo $form->field($user, 'gmt')->hiddenInput()->label('');
                        }else{
                            echo $form->field($user, 'gmt')->label('Сдвиг времени сервиса GMT') ;
                        }
            ?>


            <?= $form->field($user, 'status')->checkbox(['label'=>'Доступность контента']); ?>
            <?php if($_ENV['TYPE_AUTH'] == 'RSAA' ){
                $form->field($user, 'change_password')->checkbox(['label' => '','class'=> ' uk-hidden']);
            }else{
                echo $form->field($user, 'change_password')->checkbox(['label' => 'Cменить пароль']);
            } ?>

            <h4>Разрешить использование модулей</h4>
            <?php

             foreach ($user_modules as $key => $val){
                 echo $form->field($user, "modules[$key]")->checkbox(['label' => $name_modules[$key]]);
             }


            ?>

            <?= Html::submitButton('Сохранить',
                ['class' => 'uk-button uk-button-primary']) ?>
            <?php
            ActiveForm::end();
            ?>
        </div>
    </div>
</div>
<?php
$js = <<<JS

let chat_bot_check = $('.field-users-modules-chatbots input[type="checkbox"]');
let big_data_div = $('.field-users-modules-bigdata');
big_data_div.addClass('uk-margin-left');
checkedChatBot(chat_bot_check);

chat_bot_check.on('change', function(){
    checkedChatBot(this);
});

function checkedChatBot(element) {
    let big_data_check = $('.field-users-modules-bigdata input[type="checkbox"]');
    if ($(element).is(':checked')){
       big_data_div.removeClass('uk-hidden');
    } else {
        big_data_div.addClass('uk-hidden');
        big_data_check.prop('checked', false);
    }
}

JS;
$this->registerJs($js);


