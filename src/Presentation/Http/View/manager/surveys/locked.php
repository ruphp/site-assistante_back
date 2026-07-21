<?php

use app\Application\Panel\Dto\ClientProjectView;

/**
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = 'Анкетирование';
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <div class="uk-alert-primary" uk-alert>
        <h3>Анкетирование доступно в тарифе Start</h3>
        <p>После подключения тарифа можно создавать анкеты и показывать их посетителям на сайте.</p>
    </div>
</div>
