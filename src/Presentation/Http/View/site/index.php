<?php

/* @var $this yii\web\View */

use yii\helpers\Html;

$this->title = 'SiteWidget | Виджет-ассистент для посетителей вашего сайта';
$this->params['hideLayoutFooter'] = true;
$this->registerMetaTag([
    'name' => 'description',
    'content' => 'SiteWidget | Виджет-ассистент для посетителей вашего сайта: онлайн-поддержка, инструкции, онбординг и анкеты.',
]);
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => 'SiteWidget, виджет помощи, онлайн-поддержка, навигатор по сайту, инструкции, анкеты',
]);

$this->registerCss(<<<CSS
.tm-header,
.tm-header + .uk-container.uk-margin,
.site-landing ~ .uk-container.uk-margin,
.site-landing ~ footer {
    display: none;
}
body:has(.site-landing) > .uk-offcanvas-content > .uk-container.uk-margin {
    display: none;
}
.osn:has(.site-landing) > .uk-container,
.osn:has(.site-landing) > .uk-container-medium {
    max-width: none;
    padding-left: 0;
    padding-right: 0;
}
.osn:has(.site-landing) .uk-grid-divider {
    display: block;
    margin-left: 0;
}
.osn:has(.site-landing) .uk-grid-divider > * {
    padding-left: 0;
}
.osn:has(.site-landing) .uk-width-5-6\@s {
    width: 100%;
}
.osn:has(.site-landing) {
    margin-top: 0;
    padding-top: 0;
}
body:has(.site-landing) > .uk-offcanvas-content {
    margin-top: 0;
    padding-top: 0;
}
.site-landing {
    --sw-base: #2B245C;
    --sw-primary: #7C3AED;
    --sw-lavender: #DDD6FE;
    --sw-warm: #FBBF24;
    --sw-surface: #FAFAFF;
    --sw-text: #18181B;
    --sw-muted: #66627A;
    --sw-line: #E7E1F5;
    width: 100%;
    margin-left: 0;
    margin-top: 0;
    color: var(--sw-text);
    background: #fff;
    font-family: Arial, sans-serif;
}
.site-landing * {
    box-sizing: border-box;
}
.site-landing a {
    color: inherit;
}
.site-landing__inner {
    max-width: 1180px;
    margin: 0 auto;
    padding: 0 24px;
}
.site-landing__nav {
    position: sticky;
    top: 0;
    z-index: 20;
    display: flex;
    align-items: center;
    justify-content: space-between;
    min-height: 78px;
    background: #fff;
    border-bottom: 1px solid var(--sw-line);
}
.site-landing__brand {
    display: flex;
    align-items: center;
    gap: 14px;
    font-weight: 800;
    font-size: 22px;
    color: var(--sw-base);
    letter-spacing: 0;
}
.site-landing__mark {
    width: 69px;
    height: 69px;
    display: block;
}
.site-landing__mark img {
    display: block;
    width: 100%;
    height: 100%;
}
.site-landing__nav-links {
    display: flex;
    align-items: center;
    gap: 24px;
    color: var(--sw-muted);
    font-size: 15px;
}
.site-landing__nav-toggle {
    position: absolute;
    width: 1px;
    height: 1px;
    opacity: 0;
    pointer-events: none;
}
.site-landing__nav-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}
.site-landing__burger {
    display: none;
    width: 46px;
    height: 42px;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--sw-base);
    background: #fff;
    cursor: pointer;
}
.site-landing__burger span,
.site-landing__burger::before,
.site-landing__burger::after {
    display: block;
    width: 20px;
    height: 2px;
    background: var(--sw-base);
}
.site-landing__burger::before,
.site-landing__burger::after {
    content: '';
    position: absolute;
}
.site-landing__burger {
    position: relative;
}
.site-landing__burger::before {
    transform: translateY(-7px);
}
.site-landing__burger::after {
    transform: translateY(7px);
}
.site-landing__login {
    padding: 10px 18px;
    color: #fff !important;
    background: var(--sw-base);
    text-decoration: none;
    border: 1px solid var(--sw-base);
}
.site-landing__hero {
    display: grid;
    grid-template-columns: minmax(0, 1.05fr) minmax(360px, .95fr);
    gap: 54px;
    align-items: center;
    min-height: 650px;
    padding: 74px 0 88px;
}
.site-landing__eyebrow {
    display: inline-flex;
    padding: 8px 12px;
    background: var(--sw-surface);
    border-left: 4px solid var(--sw-warm);
    color: var(--sw-base);
    font-weight: 700;
    font-size: 14px;
    margin-bottom: 24px;
}
.site-landing h1 {
    margin: 0;
    max-width: 720px;
    font-size: 41px;
    line-height: 1.02;
    font-weight: 800;
    letter-spacing: 0;
    color: var(--sw-base);
}
.site-landing__lead {
    margin: 24px 0 0;
    max-width: 650px;
    color: #3F3A52;
    font-size: 20px;
    line-height: 1.55;
}
.site-landing__actions {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-top: 34px;
    flex-wrap: wrap;
}
.site-landing__button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 48px;
    padding: 0 22px;
    text-decoration: none;
    font-weight: 700;
    border: 1px solid var(--sw-base);
}
.site-landing__button--primary {
    color: #fff !important;
    background: var(--sw-primary);
    border-color: var(--sw-primary);
}
.site-landing__button--ghost {
    color: var(--sw-base) !important;
    background: #fff;
}
.site-landing__mockup {
    position: relative;
    min-height: 430px;
    background: linear-gradient(135deg, #fff 0%, var(--sw-surface) 100%);
    border: 1px solid var(--sw-line);
    box-shadow: 18px 18px 0 var(--sw-lavender);
    padding: 22px;
}
.site-landing__browser {
    min-height: 330px;
    background: #fff;
    border: 2px solid var(--sw-base);
}
.site-landing__browser-top {
    height: 46px;
    display: flex;
    align-items: center;
    gap: 9px;
    padding: 0 16px;
    border-bottom: 2px solid var(--sw-base);
}
.site-landing__dot {
    width: 10px;
    height: 10px;
    background: var(--sw-base);
}
.site-landing__browser-body {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    padding: 22px;
}
.site-landing__chart {
    width: min(118px, 100%);
    aspect-ratio: 1 / 1;
    border: 12px solid var(--sw-lavender);
    border-right-color: var(--sw-primary);
    border-radius: 50%;
}
.site-landing__mini-graph {
    width: min(150px, 100%);
    margin-top: 22px;
    padding: 12px 10px 10px;
    border: 2px solid var(--sw-line);
    background: var(--sw-surface);
}
.site-landing__bars {
    display: flex;
    height: 78px;
    align-items: flex-end;
    gap: 9px;
}
.site-landing__bar {
    width: 22px;
    background: var(--sw-lavender);
    border: 2px solid var(--sw-base);
}
.site-landing__bar:nth-child(1) {
    height: 38px;
}
.site-landing__bar:nth-child(2) {
    height: 62px;
    background: var(--sw-primary);
}
.site-landing__bar:nth-child(3) {
    height: 48px;
}
.site-landing__bar:nth-child(4) {
    height: 72px;
    background: var(--sw-warm);
}
.site-landing__mini-line {
    height: 8px;
    width: 72%;
    margin-top: 12px;
    background: var(--sw-base);
}
.site-landing__line {
    height: 10px;
    background: var(--sw-lavender);
    margin-bottom: 14px;
}
.site-landing__line:nth-child(2) {
    width: 72%;
    background: var(--sw-primary);
}
.site-landing__widget {
    position: absolute;
    right: -22px;
    bottom: 34px;
    width: 250px;
    background: #fff;
    border: 2px solid var(--sw-base);
    box-shadow: 10px 10px 0 var(--sw-base);
}
.site-landing__widget-head {
    height: 54px;
    display: flex;
    align-items: center;
    padding: 0 18px;
    color: #fff;
    background: var(--sw-base);
    font-weight: 700;
}
.site-landing__widget-body {
    padding: 18px;
}
.site-landing__message {
    padding: 12px;
    background: var(--sw-surface);
    border-left: 4px solid var(--sw-primary);
    color: var(--sw-base);
    font-size: 14px;
    margin-bottom: 10px;
}
.site-landing__section {
    padding: 78px 0;
    border-top: 1px solid var(--sw-line);
}
.site-landing__section--tint {
    background: var(--sw-surface);
}
.site-landing__section h2 {
    margin: 0 0 16px;
    color: var(--sw-base);
    font-size: 38px;
    line-height: 1.15;
    font-weight: 800;
    letter-spacing: 0;
}
.site-landing__section-lead {
    max-width: 760px;
    margin: 0 0 34px;
    color: var(--sw-muted);
    font-size: 18px;
    line-height: 1.55;
}
.site-landing__grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 18px;
}
.site-landing__card {
    min-height: 220px;
    padding: 24px;
    background: #fff;
    border: 1px solid var(--sw-line);
    border-top: 5px solid var(--sw-primary);
}
.site-landing__card-icon {
    width: 46px;
    height: 46px;
    display: block;
    margin-bottom: 18px;
}
.site-landing__card h3 {
    margin: 0 0 12px;
    color: var(--sw-base);
    font-size: 21px;
    line-height: 1.25;
}
.site-landing__card p {
    margin: 0;
    color: var(--sw-muted);
    line-height: 1.5;
}
.site-landing__steps {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
}
.site-landing__step {
    padding: 26px;
    color: #fff;
    background: var(--sw-base);
}
.site-landing__step strong {
    display: block;
    margin-bottom: 12px;
    color: var(--sw-warm);
    font-size: 15px;
}
.site-landing__step p {
    margin: 0;
    line-height: 1.5;
}
.site-landing__split {
    display: grid;
    grid-template-columns: .9fr 1.1fr;
    gap: 44px;
    align-items: start;
}
.site-landing__list {
    display: grid;
    gap: 12px;
    margin: 0;
    padding: 0;
    list-style: none;
}
.site-landing__list li {
    padding: 16px 18px;
    background: #fff;
    border-left: 5px solid var(--sw-primary);
    color: #343044;
}
.site-landing__cta {
    padding: 48px;
    color: #fff;
    background: var(--sw-base);
}
.site-landing__cta h2 {
    color: #fff;
}
.site-landing__cta p {
    color: #E8E4FF;
}
@media (max-width: 980px) {
    .site-landing__hero,
    .site-landing__split {
        grid-template-columns: 1fr;
    }
    .site-landing__grid,
    .site-landing__steps {
        grid-template-columns: repeat(2, 1fr);
    }
    .site-landing h1 {
        font-size: 44px;
    }
}
@media (max-width: 690px) {
    .site-landing__inner {
        padding: 0 16px;
    }
    .site-landing__nav {
        position: relative;
        min-height: 66px;
        gap: 12px;
        padding: 12px 0;
    }
    .site-landing__brand {
        gap: 9px;
        font-size: 18px;
    }
    .site-landing__mark {
        width: 46px;
        height: 46px;
    }
    .site-landing__nav-links {
        position: absolute;
        top: calc(100% + 1px);
        right: 0;
        z-index: 5;
        display: none;
        width: min(260px, calc(100vw - 32px));
        align-items: stretch;
        flex-direction: column;
        gap: 0;
        padding: 8px;
        background: #fff;
        border: 1px solid var(--sw-line);
        box-shadow: 8px 8px 0 var(--sw-lavender);
        font-size: 15px;
    }
    .site-landing__nav-links a {
        padding: 13px 12px;
        border-bottom: 1px solid var(--sw-line);
        text-decoration: none;
    }
    .site-landing__nav-links a:last-child {
        border-bottom: 0;
    }
    .site-landing__nav-toggle:checked ~ .site-landing__nav-links {
        display: flex;
    }
    .site-landing__burger {
        display: flex;
    }
    .site-landing__login {
        padding: 10px 14px;
        white-space: nowrap;
    }
    .site-landing__hero {
        grid-template-columns: 1fr;
        min-height: 0;
        gap: 32px;
        padding: 36px 0 54px;
    }
    .site-landing__eyebrow {
        margin-bottom: 18px;
        font-size: 13px;
    }
    .site-landing h1 {
        font-size: 31px;
        line-height: 1.08;
    }
    .site-landing__lead {
        margin-top: 18px;
        font-size: 17px;
        line-height: 1.45;
    }
    .site-landing__actions {
        margin-top: 24px;
    }
    .site-landing__button {
        width: 100%;
    }
    .site-landing__section {
        padding: 52px 0;
    }
    .site-landing__section h2 {
        font-size: 28px;
    }
    .site-landing__section-lead {
        font-size: 16px;
    }
    .site-landing__grid,
    .site-landing__steps {
        grid-template-columns: 1fr;
    }
    .site-landing__mockup {
        min-height: 340px;
        padding: 14px;
        box-shadow: 8px 8px 0 var(--sw-lavender);
    }
    .site-landing__browser {
        min-height: 260px;
    }
    .site-landing__browser-top {
        height: 38px;
    }
    .site-landing__browser-body {
        grid-template-columns: 120px 1fr;
        gap: 14px;
        padding: 16px;
    }
    .site-landing__chart {
        width: 120px;
    }
    .site-landing__mini-graph {
        width: 120px;
        margin-top: 14px;
    }
    .site-landing__bars {
        height: 54px;
        gap: 6px;
    }
    .site-landing__bar {
        width: 17px;
    }
    .site-landing__bar:nth-child(1) {
        height: 28px;
    }
    .site-landing__bar:nth-child(2) {
        height: 42px;
    }
    .site-landing__bar:nth-child(3) {
        height: 34px;
    }
    .site-landing__bar:nth-child(4) {
        height: 50px;
    }
    .site-landing__widget {
        position: absolute;
        right: 14px;
        bottom: 14px;
        width: min(220px, calc(100% - 42px));
        box-shadow: 6px 6px 0 var(--sw-base);
    }
    .site-landing__widget-head {
        height: 44px;
        padding: 0 14px;
        font-size: 14px;
    }
    .site-landing__widget-body {
        padding: 12px;
    }
    .site-landing__message {
        padding: 9px;
        font-size: 13px;
    }
    .site-landing__cta {
        padding: 28px 20px;
    }
}
CSS);
?>

<main class="site-landing">
    <div class="site-landing__inner">
        <header class="site-landing__nav">
            <div class="site-landing__brand" aria-label="SiteWidget">
                <span class="site-landing__mark" aria-hidden="true">
                    <img src="/img/sitewidget-logo.svg" alt="">
                </span>
                <span>Site<br>Widget</span>
            </div>
            <input class="site-landing__nav-toggle" type="checkbox" id="site-landing-menu">
            <nav class="site-landing__nav-links" aria-label="Основная навигация">
                <a href="#modules">Модули</a>
                <a href="#how">Как работает</a>
                <a href="#integrations">Интеграции</a>
            </nav>
            <div class="site-landing__nav-actions">
                <?= Html::a('Войти', ['/login'], ['class' => 'site-landing__login']) ?>
                <label class="site-landing__burger" for="site-landing-menu" aria-label="Открыть меню">
                    <span></span>
                </label>
            </div>
        </header>

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
    </div>

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
