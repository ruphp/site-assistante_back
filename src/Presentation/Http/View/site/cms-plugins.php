<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'SiteWidget | Модули и плагины для CMS';
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'Готовые модули и плагины SiteWidget для WordPress, Joomla и OpenCart: где скачать, как установить и настроить public key.',
]);

$repoUrl = 'https://github.com/ruphp/sitewidget_integrations';
$items = [
    [
        'id' => 'wordpress',
        'name' => 'WordPress',
        'type' => 'плагин',
        'version' => 'ориентир: WordPress 6.x и актуальные PHP-версии хостинга',
        'download' => 'https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-wordpress-0.1.0.zip',
        'file' => 'sitewidget-wordpress-0.1.0.zip',
        'steps' => [
            'Скачайте zip-архив плагина.',
            'Откройте админку WordPress: Плагины -> Добавить новый -> Загрузить плагин.',
            'Установите архив и активируйте плагин SiteWidget.',
            'В настройках плагина укажите public key проекта из панели SiteWidget.',
            'Сохраните настройки и проверьте появление виджета на страницах сайта.',
        ],
        'settings' => [
            'public key проекта',
            'передача id, имени и email авторизованного пользователя сайта — бесплатно',
            'роли пользователя для дополнительной фильтрации контента — только в платных тарифах',
        ],
    ],
    [
        'id' => 'joomla',
        'name' => 'Joomla',
        'type' => 'system plugin',
        'version' => 'ориентир: Joomla 4/5',
        'download' => 'https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-joomla-system-0.1.0.zip',
        'file' => 'sitewidget-joomla-system-0.1.0.zip',
        'steps' => [
            'Скачайте zip-архив system plugin.',
            'Откройте админку Joomla: Система -> Установка -> Расширения.',
            'Загрузите архив и дождитесь установки.',
            'Откройте Плагины, найдите SiteWidget и включите его.',
            'Укажите public key проекта, сохраните настройки и проверьте сайт.',
        ],
        'settings' => [
            'public key проекта',
            'передача id, имени и email авторизованного пользователя — бесплатно',
            'группы и роли для дополнительной фильтрации контента — только в платных тарифах',
        ],
    ],
    [
        'id' => 'opencart',
        'name' => 'OpenCart',
        'type' => 'модуль OCMOD',
        'version' => 'ориентир: OpenCart 3.x/4.x, старые версии лучше проверять отдельно',
        'download' => 'https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-opencart-0.1.0.ocmod.zip',
        'file' => 'sitewidget-opencart-0.1.0.ocmod.zip',
        'steps' => [
            'Скачайте архив .ocmod.zip.',
            'Откройте админку OpenCart: Extensions -> Installer.',
            'Загрузите архив модуля.',
            'Перейдите в Extensions -> Modifications и обновите кэш модификаций.',
            'Откройте настройки модуля SiteWidget, укажите public key и включите модуль.',
        ],
        'settings' => [
            'public key проекта',
            'передача id, имени и email авторизованного пользователя — бесплатно',
            'группы клиентов и роли для дополнительной фильтрации контента — только в платных тарифах',
        ],
    ],
];
?>

<main class="site-landing site-docs">
    <section class="site-landing__section site-docs__hero">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">Готовые интеграции</div>
            <h1>Модули и плагины SiteWidget для CMS</h1>
            <p class="site-landing__lead">
                Если сайт работает на WordPress, Joomla или OpenCart, виджет можно подключить без ручной вставки кода:
                установить расширение, указать public key проекта и включить нужные параметры.
            </p>
            <div class="site-landing__actions site-docs__anchors">
                <?php foreach ($items as $item): ?>
                    <a class="site-landing__button site-landing__button--ghost" href="#<?= Html::encode($item['id']) ?>">
                        <?= Html::encode($item['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Общий порядок подключения</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>01. Создайте проект</strong>
                    <p>В панели SiteWidget создайте проект для сайта и скопируйте его public key.</p>
                </div>
                <div class="site-landing__step">
                    <strong>02. Установите расширение</strong>
                    <p>Скачайте архив для своей CMS и установите его штатным способом через админку сайта.</p>
                </div>
                <div class="site-landing__step">
                    <strong>03. Проверьте виджет</strong>
                    <p>Укажите public key, сохраните настройки и откройте публичную страницу сайта.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner site-docs__grid">
            <?php foreach ($items as $item): ?>
                <article class="site-docs__card" id="<?= Html::encode($item['id']) ?>">
                    <div class="site-docs__card-head">
                        <div>
                            <span class="site-docs__type"><?= Html::encode($item['type']) ?></span>
                            <h2><?= Html::encode($item['name']) ?></h2>
                        </div>
                        <?= Html::a('Скачать', $item['download'], [
                            'class' => 'site-landing__button site-landing__button--primary',
                            'download' => true,
                        ]) ?>
                    </div>

                    <p class="site-docs__version"><?= Html::encode($item['version']) ?></p>
                    <p class="site-docs__file">Файл: <code><?= Html::encode($item['file']) ?></code></p>

                    <div class="site-docs__columns">
                        <div>
                            <h3>Установка</h3>
                            <ol class="site-docs__list">
                                <?php foreach ($item['steps'] as $step): ?>
                                    <li><?= Html::encode($step) ?></li>
                                <?php endforeach; ?>
                            </ol>
                        </div>
                        <div>
                            <h3>Настройки</h3>
                            <ul class="site-docs__list">
                                <?php foreach ($item['settings'] as $setting): ?>
                                    <li><?= Html::encode($setting) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>Исходники и новые версии</h2>
                <p class="site-landing__section-lead">
                    Актуальные архивы, исходники и изменения по интеграциям хранятся в отдельной открытой репе.
                </p>
                <?= Html::a('Открыть GitHub', $repoUrl, [
                    'class' => 'site-landing__button site-landing__button--primary',
                    'target' => '_blank',
                    'rel' => 'noopener',
                ]) ?>
            </div>
        </div>
    </section>
</main>
