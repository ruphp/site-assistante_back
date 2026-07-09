<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'SiteWidget | Виджет-ассистент для посетителей вашего сайта';
$this->registerMetaTag([
        'name' => 'description',
        'content' => 'SiteWidget | Виджет-ассистент для посетителей вашего сайта: онлайн-поддержка, инструкции, онбординг и анкеты.',
]);
$this->registerMetaTag([
        'name' => 'keywords',
        'content' => 'SiteWidget, виджет помощи, онлайн-поддержка, навигатор по сайту, инструкции, анкеты',
]);
?>

<main class="site-landing">
    <section class="site-landing__hero">
        <div>
            <div class="site-landing__eyebrow">Когда сайт сложный, а путь должен быть понятным</div>
            <h1>Виджет-ассистент для посетителей вашего сайта</h1>
            <p class="site-landing__lead">
                SiteWidget подключается одной вставкой JavaScript и помогает пользователям сайта не теряться:
                онлайн-чат с оператором, инструкции, навигатор по страницам и анкеты работают в одном виджете.
            </p>
            <div class="site-landing__actions">
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
                <a class="site-landing__button site-landing__button--ghost" href="#modules">Посмотреть модули</a>
            </div>
        </div>

        <div class="site-landing__mockup" aria-label="Пример виджета на сайте">
            <div class="site-landing__browser">
                <div class="site-landing__browser-top">
                    <span class="site-landing__dot"></span>
                    <span class="site-landing__dot"></span>
                    <span class="site-landing__dot"></span>
                </div>
                <div class="site-landing__browser-body">
                    <div>
                        <div class="site-landing__chart"></div>
                        <div class="site-landing__mini-graph" aria-hidden="true">
                            <div class="site-landing__bars">
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                                <span class="site-landing__bar"></span>
                            </div>
                            <div class="site-landing__mini-line"></div>
                        </div>
                    </div>
                    <div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                        <div class="site-landing__line"></div>
                    </div>
                </div>
            </div>
            <div class="site-landing__widget">
                <div class="site-landing__widget-head">Онлайн-поддержка</div>
                <div class="site-landing__widget-body">
                    <div class="site-landing__message">Операторы онлайн</div>
                    <div class="site-landing__message">Чем помочь на этой странице?</div>
                </div>
            </div>
        </div>
    </section>

    <section id="modules" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <h2>Модули, которые закрывают частые вопросы пользователей сайта</h2>
            <p class="site-landing__section-lead">
                Каждый модуль можно включать владельцу сайта по тарифу и настраивать отдельно. На старте продукт держит
                обязательный минимум поддержки, а остальные сценарии расширяют самообслуживание.
            </p>
            <div class="site-landing__grid">
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-support.svg" alt="">
                    <h3>Онлайн-поддержка</h3>
                    <p>Чат с оператором, обращения как тикеты, быстрые кнопки тем, уведомления менеджерам и контроль ожидания ответа.</p>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-instructions.svg" alt="">
                    <h3>Инструкции</h3>
                    <p>База знаний внутри виджета: разделы, иллюстрированные статьи и короткие материалы. Возможность добавления в избранное.</p>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-module-navigator.svg" alt="">
                    <h3>Онбординг</h3>
                    <p>Навигатор по страницам и подсказки к элементам, чтобы сложить сложный сценарий как пазл и провести пользователя сайта до результата.</p>
                </article>
                <article class="site-landing__card">
                    <img class="site-landing__card-icon" src="/img/sitewidget-checkbox.svg" alt="">
                    <h3>Анкетирование</h3>
                    <p>Пошаговые анкеты с разными типами вопросов, с возможностью вернуться к ним позже, удобный конструктор форм.</p>
                </article>
            </div>
        </div>
    </section>

    <section id="how" class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Как это работает</h2>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>01. Подключение</strong>
                    <p>Владелец сайта вставляет JS-код. Виджет получает конфигурацию по public key и домену сайта и выводит нужный контент.</p>
                </div>
                <div class="site-landing__step">
                    <strong>02. Настройка</strong>
                    <p>В панели владельца сайта настраиваются оформление, модули, расписание операторов, уведомления и роли.</p>
                </div>
                <div class="site-landing__step">
                    <strong>03. Работа</strong>
                    <p>Пользователь сайта получает помощь на месте, а владелец сайта видит обращения, метрики и другие отчёты.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="android-app" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner">
            <div class="site-landing__app-head">
                <div>
                    <h2>Приложение для менеджера</h2>
                    <p class="site-landing__section-lead">
                        Android-приложение помогает не пропускать обращения: менеджер получает уведомления о новых
                        и неотвеченных диалогах, даже если сайт открыт не на компьютере.
                    </p>
                </div>
                <?= Html::a('Скачать APK', ['/sitewidgetmanager.apk'], [
                    'class' => 'site-landing__button site-landing__button--primary',
                    'download' => true,
                ]) ?>
            </div>

            <div class="site-landing__install-grid">
                <article class="site-landing__install-card">
                    <img src="/img/android-install/check.webp" alt="Проверка приложения Google Play Защитой">
                    <div>
                        <strong>01. Скачайте файл</strong>
                        <p>Откройте ссылку на APK с телефона. Если Android предложит проверку, запустите ее или продолжите установку.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/checking.webp" alt="Процесс проверки приложения">
                    <div>
                        <strong>02. Дождитесь проверки</strong>
                        <p>Проверка может занять немного времени. После нее Android покажет, можно ли установить приложение.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/safe.webp" alt="Приложение безопасное">
                    <div>
                        <strong>03. Разрешите установку</strong>
                        <p>Если установка из браузера еще не разрешена, Android попросит разрешить установку из неизвестного источника.</p>
                    </div>
                </article>
                <article class="site-landing__install-card">
                    <img src="/img/android-install/installed.webp" alt="Приложение установлено">
                    <div>
                        <strong>04. Откройте приложение</strong>
                        <p>После установки нажмите «Открыть», войдите в аккаунт владельца сайта и разрешите уведомления.</p>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section id="integrations" class="site-landing__section site-landing__section--tint">
        <div class="site-landing__inner site-landing__split">
            <div>
                <h2>Готовые модули для CMS и интернет-магазинов</h2>
                <p class="site-landing__section-lead">
                    Для WordPress, Joomla и OpenCart можно подключить SiteWidget без ручной интеграции: модуль сам
                    передаст нужные данные сайта, пользователя и ролей.
                </p>
            </div>
            <ul class="site-landing__list">
                <li>Быстрое подключение SiteWidget к WordPress, Joomla и OpenCart.</li>
                <li>Интеграция авторизованного посетителя с виджетом без лишней настройки кода.</li>
                <li>Настройка доступа к контенту по ролям пользователя сайта.</li>
            </ul>
            <div class="site-landing__download">
                <a href="https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-wordpress-0.1.0.zip">
                    <strong>WordPress</strong>
                    <span>Скачать плагин .zip</span>
                </a>
                <a href="https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-joomla-system-0.1.0.zip">
                    <strong>Joomla</strong>
                    <span>Скачать system plugin .zip</span>
                </a>
                <a href="https://github.com/ruphp/sitewidget_integrations/raw/main/dist/sitewidget-opencart-0.1.0.ocmod.zip">
                    <strong>OpenCart</strong>
                    <span>Скачать модуль .ocmod.zip</span>
                </a>
                <a href="https://github.com/ruphp/sitewidget_integrations">
                    <strong>GitHub</strong>
                    <span>Исходники и инструкции</span>
                </a>
            </div>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <h2>Контроль после подключения</h2>
            <p class="site-landing__section-lead">
                Владелец сайта видит не только обращения, но и то, как посетители взаимодействуют с виджетом.
            </p>
            <div class="site-landing__steps">
                <div class="site-landing__step">
                    <strong>История</strong>
                    <p>Хранение истории взаимодействия посетителей с виджетом.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Отчетность</strong>
                    <p>Настраиваемая отчетность по обращениям, модулям и активности.</p>
                </div>
                <div class="site-landing__step">
                    <strong>Понимание</strong>
                    <p>Видно, где посетители чаще всего застревают и какие сценарии требуют доработки.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="site-landing__section">
        <div class="site-landing__inner">
            <div class="site-landing__cta">
                <h2>SiteWidget делает сложный сайт понятнее</h2>
                <p class="site-landing__section-lead">
                    Онлайн-поддержка доступна как базовый модуль, а инструкции, онбординг, анкеты, лимиты и дополнительные
                    возможности подключаются по тарифу.
                </p>
                <?= Html::a('Начать использовать', ['/join'], ['class' => 'site-landing__button site-landing__button--primary']) ?>
            </div>
        </div>
    </section>
</main>
