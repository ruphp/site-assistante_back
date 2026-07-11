<?php

use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Инструкции SiteWidget';
$this->registerMetaTag([
        'name' => 'description',
        'content' => 'Инструкции SiteWidget: подключение виджета, Android-приложение, Telegram-бот и CMS-модули.',
]);
?>

<main class="site-landing">
    <section class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__eyebrow">Инструкции SiteWidget</div>
            <h1>Подключение, приложение и модули</h1>
            <p class="site-landing__lead">
                Здесь собраны общие инструкции. Актуальный public key и готовый код подключения находятся в личном
                кабинете после регистрации.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Войти в панель', ['/login'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <?= Html::a('Создать аккаунт', ['/join'], ['class' => 'site-landing__button site-landing__button--ghost']) ?>
            </div>
        </div>
    </section>

    <section id="connect" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Как подключить виджет</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>01. Создайте проект</strong>
                    <p>После регистрации откройте панель управления и создайте проект для сайта.</p>
                </div>
                <div class="site-landing__step">
                    <strong>02. Укажите домен</strong>
                    <p>В параметрах проекта укажите домен сайта, где будет работать виджет.</p>
                </div>
                <div class="site-landing__step">
                    <strong>03. Вставьте код</strong>
                    <p>Скопируйте код подключения с актуальным public key и разместите его перед закрывающим тегом <code>&lt;/body&gt;</code>.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="android" class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__app-head">
                <div>
                <h2>Android-приложение для менеджера</h2>
                <p class="site-landing__section-lead">
                    Приложение показывает открытые диалоги, даёт отвечать посетителям и присылает push-уведомления о
                    новых и неотвеченных обращениях.
                </p>
                </div>
                <div class="site-landing__actions">
                    <?= Html::a('Скачать APK', ['/sitewidgetmanager.apk'], [
                            'class' => 'site-landing__button site-landing__button--primary',
                            'download' => true,
                    ]) ?>
                </div>
            </div>

            <div class="site-landing__install-grid">
                <article class="site-landing__install-card">
                    <img src="/img/android-install/check.webp" alt="Рекомендуется проверка приложения">
                    <div>
                        <strong>01. Скачайте APK</strong>
                        <p>Откройте ссылку на APK с телефона. Если Android предложит проверку, запустите её.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/checking.webp" alt="Проверка приложения Google Play Защитой">
                    <div>
                        <strong>02. Дождитесь проверки</strong>
                        <p>Проверка может занять немного времени. После неё Android покажет, можно ли установить приложение.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/safe.webp" alt="Приложение безопасное">
                    <div>
                        <strong>03. Разрешите установку</strong>
                        <p>Если установка из браузера отключена, Android попросит разрешить установку из неизвестного источника.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/installed.webp" alt="Приложение установлено">
                    <div>
                        <strong>04. Откройте приложение</strong>
                        <p>После установки войдите в аккаунт владельца сайта и разрешите уведомления.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="telegram" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Telegram-бот для менеджеров</h2>
                <p class="site-landing__section-lead">
                    Бот нужен как быстрый канал для уведомлений и ответов, когда Android-приложение неудобно или
                    недоступно.
                </p>
                <div class="site-landing__actions">
                    <?= Html::a('Открыть @SiteWidgetBot', 'https://t.me/SiteWidgetBot', [
                            'class' => 'site-landing__button site-landing__button--primary',
                            'target' => '_blank',
                            'rel' => 'noopener',
                    ]) ?>
                </div>
            </div>
            <ul class="site-landing__list">
                <li>В личном кабинете откройте раздел менеджеров и получите код привязки Telegram.</li>
                <li>Откройте бота и отправьте команду <code>/start CODE</code>.</li>
                <li>Команда <code>/dialogs</code> показывает горячие открытые диалоги.</li>
                <li>Ответы из Telegram уходят посетителю в тот же диалог виджета.</li>
            </ul>
        </div>
    </section>

    <section id="cms" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>CMS-модули</h2>
            <p class="site-landing__section-lead">
                Для WordPress, Joomla и OpenCart есть готовые модули подключения. В настройках модуля достаточно
                указать public key проекта.
            </p>

            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>WordPress</strong>
                    <p>Скачайте ZIP-плагин, установите его через раздел «Плагины», активируйте и укажите public key.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Joomla</strong>
                    <p>Установите ZIP-пакет как system plugin, включите плагин в админке и заполните public key проекта.</p>
                </div>
                <div class="site-landing__step">
                    <strong>OpenCart</strong>
                    <p>Загрузите модуль .ocmod.zip, обновите модификаторы, включите модуль и укажите public key витрины.</p>
                </div>
            </div>

            <div class="site-landing__business">
                <div>
                    <strong>Где взять public key</strong>
                    <p>Войдите в личный кабинет, откройте «Панель управления виджетом» или «Параметры» нужного проекта.</p>
                </div>
            </div>

            <div class="site-landing__actions">
                <?= Html::a('Открыть модули CMS', ['/cms-plugins'], [
                        'class' => 'site-landing__button site-landing__button--ghost',
                ]) ?>
            </div>
        </div>
    </section>

    <section id="support" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Онлайн-поддержка и быстрые кнопки</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>Настройки поддержки</strong>
                    <p>В личном кабинете задаются приветствие, расписание операторов, настройка автооткрытия виджета и данные посетителя.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Кнопки быстрых обращений</strong>
                    <p>Кнопки помогают посетителю начать диалог с понятной темой: доставка, оплата, подбор товара.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Ответ менеджера</strong>
                    <p>Менеджер отвечает из панели, Android-приложения или Telegram-бота. Вся история обращений хранится в панели управления виджетом. Лимиты ответов зависят от тарифа.</p>
                </div>
            </div>
        </div>
    </section>
</main>
