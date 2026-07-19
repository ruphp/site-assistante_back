<?php

use app\Application\Panel\Dto\ClientProjectView;

/**
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Онбординг';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>

    <div class="uk-alert-primary" uk-alert>
        <h3>Онбординг доступен в тарифе Start</h3>
        <p>Модуль онбординга и подсказок помогает провести посетителя по сложному сценарию внутри сайта.</p>
    </div>
</div>
