<?php

/* @var $this yii\web\View */

use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

$this->title = 'Слой поддержки пользователей на Вашем web-ресурсе';
$this->registerMetaTag([
    'name'    => 'description',
    'content' => 'Слой поддержки пользователей на Вашем web-ресурсе',
]);
$this->registerMetaTag([
    'name'    => 'keywords',
    'content' => 'Слой поддержки пользователей на Вашем web-ресурсе',
]);
?>
<div class="uk-section  uk-section-default">
    <div class="uk-container">

        <p class="uk-text-center  big-text uk-margin-remove">Help Layer</p>
        <p class="uk-text-center logo-text uk-margin-remove">Контекстная поддержка пользователей на вашем
            web-ресурсе</p>
        <br>
        <br>
        <br>
        <p class="uk-text-center uk-margin-remove">
            <iframe width="560" height="315" src="https://www.youtube.com/embed/9I0DqFCSAdY" frameborder="0"
                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen></iframe>
        </p>
        <br>
        <p class="uk-text-center">
            Хотите попробовать? Напишите нам!<br><br>
            <a class="uk-button uk-button-primary uk-margin-small-right " href="#new-form" uk-scroll>
                НАПИСАТЬ
            </a>
        </p>
    </div>
</div>
<div class="uk-section  uk-section-primary">
    <div class="uk-container">
        <div class="uk-grid-divider uk-child-width-expand@s" uk-grid>
            <div class="uk-text-center uk-h2">HELP CENTER</div>
            <div class="uk-text-center uk-h2">SMARTIUS LMS</div>
            <div class="uk-text-center uk-h2">HIGHLIGHTS</div>
        </div>
    </div>
</div>
<div class="uk-section  uk-section-default">
    <div class="uk-container">
        <div class="uk-child-width-expand@m" uk-grid>
            <div class="uk-grid-item-match uk-margin-auto-vertical">
                <div class="uk-card">
                    <img src="/img/img1.png" alt="">
                </div>
            </div>
            <div class="uk-card-body uk-margin-auto-vertical">
                <h3>HELP CENTER</h3>
                <p class="uk-text-lead">Kонтекстная помощь здесь и сейчас</p>
                <p>Обеспечивает мгновенную контекстную поддержку на Вашем ресурсе</p>
                <p>Больше не нужно отправлять пользователей за пределы сайта, долго ждать поддержки или отчета
                    чат-бота</p>
                <p>Полная база знаний в виде микрокурсов о Вашем ресурсе, для каждого пользователя в любое время</p>
            </div>
        </div>
        <div class="uk-child-width-expand@m" uk-grid uk-flex>
            <div class="uk-grid-item-match uk-margin-auto-vertical">
                <div class="uk-card">
                    <img src="/img/img2.png" alt="">
                </div>
            </div>
            <div class="uk-card-body uk-margin-auto-vertical uk-flex-first@m">
                <h3>SMARTIUS LMS</h3>
                <p class="uk-text-lead">Непрерывный процесс обучения пользователей</p>
                <p>Learning Management System - упрощенная система дистанционного обучения, позволяет создавать
                    дистанционные курсы, тесты и опросы</p>
                <p>Система поддерживает современные форматы публикации курсов дистанционного обучения</p>
                <p>Расширенная база знаний о Вашем ресурсе, бизнес-процессах и особенностях работы с ним</p>
            </div>
        </div>
        <div class="uk-child-width-expand@m" uk-grid>
            <div class="uk-grid-item-match uk-margin-auto-vertical">
                <div class="uk-card">
                    <img src="/img/img3.png" alt="">
                </div>
            </div>
            <div class="uk-card-body uk-margin-auto-vertical">
                <h3>HIGHLIGHTS</h3>
                <p class="uk-text-lead">Интерактивные подсказки для интерфейса</p>
                <p>Позволяют выделить и пояснить отдельные области на Вашем ресурсе</p>
                <p>Внимание пользователей сфокусировано на нужных деталях во время онбординга и вовлечения</p>
                <p>Интерактивные элементы даже для статичного интерфейса</p>
            </div>
        </div>
        <div id="new-form" class="uk-margin-auto uk-width-1-2 ">
            <br>
            <h3 class="uk-text-center">Напишите нам</h3>
            <p class="uk-text-center">
                <?php
                app\Presentation\Yii\Asset\AppAsset::register($this);
                $form = ActiveForm::begin(['id' => 'user-join-form', 'classForm' => 'uk-form-stacked']); ?>
                <?= $form->field($model, 'name')->label('Имя') ?>
                <?= $form->field($model, 'email')->label('Адрес электронной почты') ?>
                <?= $form->field($model, 'phone')->label('Номер телефона') ?>
                <?= $form->field($model, 'firm')->label('Сообщение')->textarea(['rows' => 4,  'class' => 'uk-textarea'])  ?>
                <?= Html::submitButton('Отправить',
                    ['class' => 'uk-button uk-button-primary']) ?>
                <?php ActiveForm::end(); ?>
            </p>
        </div>

    </div>
</div>


