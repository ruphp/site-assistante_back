<?php

/* @var $this \yii\web\View */

/* @var $content string */

use app\Presentation\Yii\Asset\AppAsset;
use app\Presentation\Yii\Asset\CodemirrorAsset;
use app\Presentation\Yii\Widget\Alert;
use ruwmapps\yii2_uikit3\Nav;
use ruwmapps\yii2_uikit3\NavBar;
use ruwmapps\yii2_uikit3\Offcanvas;
use ruwmapps\yii2_uikit3\UikitAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;

UikitAsset::register($this);
AppAsset::register($this);
CodemirrorAsset::register($this);

$email_user = '';
$name_user = '';
$id_user = null;
$isOwner = false;
$route = Yii::$app->requestedRoute ?? '';
$controllerRoute = Yii::$app->controller?->route ?? '';
$requestPath = trim((string)(parse_url(Yii::$app->request->url, PHP_URL_PATH) ?? ''), '/');
$requestRoutePath = trim((string)Yii::$app->request->pathInfo, '/');
$isAuthPage = in_array($route, ['site/login', 'site/join', 'site/send-email', 'site/auth'], true);
$isLandingPage = in_array($route, ['site/index', 'site/index/'], true)
    || in_array($controllerRoute, ['site/index', 'site/index/'], true)
    || $requestRoutePath === 'site/index'
    || $requestPath === ''
    || $requestPath === 'index.php';
$isPublicModulePage = in_array($route, ['site/faq-instructions', 'site/onboarding', 'site/surveys'], true)
    || in_array($controllerRoute, ['site/faq-instructions', 'site/onboarding', 'site/surveys'], true)
    || in_array($requestRoutePath, ['faq-instructions', 'onboarding', 'surveys'], true)
    || in_array($requestPath, ['faq-instructions', 'onboarding', 'surveys'], true);

if (Yii::$app->user->isGuest) {
    $role = [1];
} else {
    $role = [2];
}
$role = Yii::$app->request->get()['roles'] ?? $role;
$testchatbots = Yii::$app->request->get()['testchatbots'] ?? 0;
$str_role = implode(",", $role);

if (Yii::$app->user->isGuest && $isLandingPage) {
    $menu = [
        [
            'label' => 'Модули',
            'url' => '/#modules',
            'items' => [
                ['label' => 'Онлайн-поддержка', 'url' => '/#support'],
                ['label' => 'FAQ и инструкции', 'url' => ['/faq-instructions']],
                ['label' => 'Онбординг', 'url' => ['/onboarding']],
                ['label' => 'Опросы и анкеты', 'url' => ['/surveys']],
            ],
        ],
        ['label' => 'Как работает', 'url' => '/#how'],
        ['label' => 'Интеграции', 'url' => '/#integrations'],
        ['label' => 'Приложение', 'url' => '/#android-app'],
    ];
} elseif (Yii::$app->user->isGuest && $isPublicModulePage) {
    $menu = [
        ['label' => 'Главная', 'url' => ['/']],
        [
            'label' => 'Модули',
            'url' => '/#modules',
            'items' => [
                ['label' => 'Онлайн-поддержка', 'url' => '/#support'],
                ['label' => 'FAQ и инструкции', 'url' => ['/faq-instructions']],
                ['label' => 'Онбординг', 'url' => ['/onboarding']],
                ['label' => 'Опросы и анкеты', 'url' => ['/surveys']],
            ],
        ],
        ['label' => 'Как работает', 'url' => '#how'],
        ['label' => 'Для чего', 'url' => '#use-cases'],
    ];
} elseif (Yii::$app->user->isGuest) {
    if ($isAuthPage) {
        $menu = [
            ['label' => 'Регистрация', 'url' => ['/join']],
            ['label' => 'Главная', 'url' => ['/']],
        ];
    } else {
        $menu = [
            ['label' => 'Главная', 'url' => ['/']],
        ];
    }
} else {
    $email_user = Yii::$app->user->identity->email;
    $name_user = Yii::$app->user->identity->name;
    $id_user = Yii::$app->user->identity->id;
    $isOwner = (int)$id_user === (int)Yii::$app->user->identity->getPublicKey();
    $assignments = Yii::$app->authManager === null ? [] : Yii::$app->authManager->getAssignments(Yii::$app->user->id);
    $isAdmin = isset($assignments['admin']);
    $activeProjectId = (int)Yii::$app->request->get('projectId') ?: null;
    $projectParam = $activeProjectId === null ? [] : ['projectId' => $activeProjectId];
    $menu = $isAdmin ? [
        ['label' => 'Клиенты', 'url' => ['/admin/clients']],
    ] : array_merge(
        [
            ['label' => 'Мои проекты', 'url' => ['/manager']],
        ],
        $activeProjectId === null ? [] : [
            [
                'label' => 'Параметры',
                'url' => ['/manager/params'] + $projectParam,
                'items' => [
                    ['label' => 'Подключение', 'url' => ['/manager/params'] + $projectParam],
                    ['label' => 'Оформление', 'url' => ['/manager/designe'] + $projectParam],
                    ['label' => 'Лимиты', 'url' => ['/manager/limits'] + $projectParam],
                ],
            ],
            [
                'label' => 'Онлайн-поддержка',
                'url' => ['/manager/support/conversations'] + $projectParam,
                'items' => array_values(array_filter([
                    ['label' => 'Диалоги', 'url' => ['/manager/support/conversations'] + $projectParam],
                    ['label' => 'Кнопки быстрых обращений', 'url' => ['/manager/support/entry-points'] + $projectParam],
                    $isOwner ? ['label' => 'Менеджеры', 'url' => ['/manager/operators'] + $projectParam] : null,
                    ['label' => 'Настройки', 'url' => ['/manager/support'] + $projectParam],
                ])),
            ],
            [
                'label' => 'Инструкции',
                'url' => ['/manager/instructions'] + $projectParam,
                'items' => [
                    ['label' => 'Все инструкции', 'url' => ['/manager/instructions'] + $projectParam],
                    ['label' => 'Разделы', 'url' => ['/manager/instructions/sections'] + $projectParam],
                    ['label' => 'Создать инструкцию', 'url' => ['/manager/instructions/create'] + $projectParam],
                ],
            ],
            [
                'label' => 'Онбординг',
                'url' => ['/manager/onboarding'] + $projectParam,
                'items' => [
                    ['label' => 'Сценарии', 'url' => ['/manager/onboarding'] + $projectParam],
                    ['label' => 'Подсказки', 'url' => ['/manager/onboarding/hints'] + $projectParam],
                    ['label' => 'Создать сценарий', 'url' => ['/manager/onboarding/create'] + $projectParam],
                ],
            ],
            [
                'label' => 'Анкетирование',
                'url' => ['/manager/surveys'] + $projectParam,
                'items' => [
                    ['label' => 'Анкеты', 'url' => ['/manager/surveys'] + $projectParam],
                    ['label' => 'Создать анкету', 'url' => ['/manager/surveys/create'] + $projectParam],
                ],
            ],
        ]
    );
    $menu = array_values(array_filter($menu));
}

$id_user = Yii::$app->request->get()['id_user'] ?? $id_user;
$request = Yii::$app->request;
$siteBaseUrl = 'https://sitewidget.ru';
$isPublicSeoPage = $isLandingPage || $isPublicModulePage || in_array($requestPath, ['cms-plugins'], true);
$seoDescription = $this->params['seoDescription'] ?? null;
$seoCanonical = $this->params['seoCanonical'] ?? ($isLandingPage ? '/' : ('/' . $requestPath));
$seoCanonicalUrl = strpos((string)$seoCanonical, 'http') === 0
    ? (string)$seoCanonical
    : $siteBaseUrl . '/' . ltrim((string)$seoCanonical, '/');
$seoImageUrl = $siteBaseUrl . ($this->params['seoImage'] ?? '/img/sitewidget-logo.svg');
$seoRobots = $this->params['seoRobots'] ?? ($isPublicSeoPage ? 'index,follow' : 'noindex,nofollow');
$seoSchemas = $this->params['seoSchemas'] ?? [];

if ($isPublicSeoPage) {
    $seoSchemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => 'SiteWidget',
        'url' => $siteBaseUrl,
        'logo' => $siteBaseUrl . '/img/sitewidget-logo.svg',
        'email' => 'sitewidget@ya.ru',
    ];
    $seoSchemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => 'SiteWidget',
        'url' => $siteBaseUrl,
    ];
}

if (!empty($this->params['seoBreadcrumbs']) && is_array($this->params['seoBreadcrumbs'])) {
    $seoSchemas[] = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => array_map(
            static fn(array $item, int $index): array => [
                '@type' => 'ListItem',
                'position' => $index + 1,
                'name' => (string)($item['name'] ?? ''),
                'item' => $siteBaseUrl . '/' . ltrim((string)($item['url'] ?? '/'), '/'),
            ],
            $this->params['seoBreadcrumbs'],
            array_keys($this->params['seoBreadcrumbs'])
        ),
    ];
}

$this->beginPage();

?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <link rel="canonical" href="<?= Html::encode($seoCanonicalUrl) ?>">
        <meta name="robots" content="<?= Html::encode($seoRobots) ?>">
        <meta property="og:locale" content="ru_RU">
        <meta property="og:site_name" content="SiteWidget">
        <meta property="og:type" content="<?= Html::encode($this->params['seoOgType'] ?? 'website') ?>">
        <meta property="og:title" content="<?= Html::encode($this->title) ?>">
        <?php if ($seoDescription !== null): ?>
            <meta property="og:description" content="<?= Html::encode($seoDescription) ?>">
            <meta name="twitter:description" content="<?= Html::encode($seoDescription) ?>">
        <?php endif; ?>
        <meta property="og:url" content="<?= Html::encode($seoCanonicalUrl) ?>">
        <meta property="og:image" content="<?= Html::encode($seoImageUrl) ?>">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="<?= Html::encode($this->title) ?>">
        <meta name="twitter:image" content="<?= Html::encode($seoImageUrl) ?>">
        <?php foreach ($seoSchemas as $schema): ?>
            <script type="application/ld+json"><?= Json::htmlEncode($schema) ?></script>
        <?php endforeach; ?>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
        <link rel="icon" type="image/svg+xml" href="/favicon-dark.svg" media="(prefers-color-scheme: dark)">
        <?php $this->head() ?>
        <meta name="yandex-verification" content="7c5f49e6578a5ddc" />
    </head>
    <body>
    <?php $this->beginBody() ?>

    <div class="uk-offcanvas-content">

        <header class="sw-header" id="sw-header">
            <div class="sw-header__inner">
                <a href="/" class="sw-header__brand" aria-label="SiteWidget">
                    <span class="sw-header__mark" aria-hidden="true">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="399 142 52 60" fill="none" class="sw-logo" width="46" height="46">
        <g class="sw-logo__puzzle">
            <path d="M447.035 164.164C447.035 162.375 445.933 161.663 444.609 161.268C444.609 161.268 435.667 162.153 432.486 162.523C429.977 162.814 428.662 160.898 430.634 157.752C431.953 155.649 432.486 154.869 432.486 153.063C432.486 149.144 429.23 145.968 425.213 145.968C421.195 145.968 417.941 149.144 417.941 153.063C417.941 154.842 418.555 156.059 419.758 157.757C422.031 160.972 420.667 162.708 417.941 162.523C414.954 162.321 405.816 161.268 405.816 161.268C404.224 161.008 403.581 162.338 403.392 164.09C403.392 164.09 402.899 166.117 402.127 176.368C402.066 176.604 402.122 176.867 402.141 176.973C402.559 179.118 404.78 177.787 407.072 176.16C408.547 175.113 410.097 174.35 411.921 174.35C415.938 174.35 419.195 177.524 419.195 181.444C419.195 185.364 415.938 188.539 411.921 188.539C409.988 188.539 408.651 187.938 406.944 186.594C405.233 185.249 402.899 183.772 402.217 185.435C402.095 185.73 402.064 186.323 402.126 186.703C402.835 194.083 403.116 194.434 403.389 195.634C403.667 196.836 404.477 198 405.815 198H443.699C445.859 198 447.035 196.707 447.035 194.749C447.035 193.715 447.035 165.951 447.035 164.164Z"
                  fill="#7C3AED" stroke="#2B245C" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
        </g>
    </svg>
</span>
                    <span class="sw-header__logo-text">
    <span class="sw-header__logo-line">Site</span>
    <span class="sw-header__logo-line">Widget</span>
</span>
                </a>
                <nav class="sw-header__nav" aria-label="Основная навигация">
                    <?php foreach ($menu as $item): ?>
                        <?php if (isset($item['items'])): ?>
                            <div class="uk-inline">
                                <a class="sw-header__nav-group" href="<?= Html::encode(Url::to($item['url'])) ?>"><?= Html::encode($item['label']) ?></a>
                                <div uk-dropdown="mode: hover; pos: bottom-left; offset: 10" class="sw-header__nav-dropdown">
                                    <?php foreach ($item['items'] as $child): ?>
                                        <a href="<?= Html::encode(Url::to($child['url'])) ?>"><?= Html::encode($child['label']) ?></a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <a href="<?= Html::encode(Url::to($item['url'])) ?>"><?= Html::encode($item['label']) ?></a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </nav>
                <div class="sw-header__actions">
                    <?php if ($isLandingPage): ?>
                        <?php if (!Yii::$app->user->isGuest): ?>
                            <?php
                            $landingAssignments = Yii::$app->authManager === null ? [] : Yii::$app->authManager->getAssignments(Yii::$app->user->id);
                            $landingCabinetUrl = isset($landingAssignments['admin']) ? ['/admin/clients'] : ['/manager'];
                            ?>
                            <a class="sw-header__login" href="<?= Html::encode(Url::to($landingCabinetUrl)) ?>">Личный кабинет</a>
                        <?php else: ?>
                            <a class="sw-header__login" href="/login">Войти</a>
                        <?php endif; ?>
                    <?php elseif (Yii::$app->user->isGuest): ?>
                        <a class="sw-header__login" href="/login">Войти</a>
                    <?php else: ?>
                        <?php $profileUrl = ($isAdmin ?? false) ? '/admin/clients' : '/manager/profile'; ?>
                        <a class="sw-header__user" href="<?= Html::encode($profileUrl) ?>"><?= Html::encode($name_user) ?></a>
                        <a class="sw-header__logout" href="/logout">Выход</a>
                    <?php endif; ?>
                    <button class="sw-header__burger" type="button" uk-toggle="target: #offcanvas"
                            aria-label="Открыть меню">
                        <span></span>
                    </button>
                </div>
            </div>
        </header>

        <div class="uk-container uk-margin">
            <?= Alert::widget() ?>
        </div>

        <section class="osn uk-section uk-section-default uk-margin-remove uk-padding-remove"
                 uk-height-viewport="expand:true">
            <div class="uk-container uk-container-medium">
                <div class="uk-width-1-1">
                    <?= $content ?>
                </div>
            </div>
        </section>
        <?php if (empty($this->params['hideLayoutFooter'])): ?>
            <footer class="sw-footer uk-margin-top">
                <div class="sw-footer__inner">
                    <div class="sw-footer__col">
                        <span class="sw-footer__brand">SiteWidget</span>
                        <span class="sw-footer__copy">&copy; <?= date('Y') ?></span>
                    </div>
                    <div class="sw-footer__col">
                        <div class="uk-text-center"><a href="mailto:sitewidget@ya.ru">sitewidget@ya.ru</a></div>
                    </div>
                    <div class="sw-footer__col">
                        <a href="/files/pzpd.docx">Политика конфиденциальности</a>
                    </div>
                </div>
            </footer>
        <?php endif; ?>
        <!-- Cookie consent -->
        <div class="sw-cookie" id="sw-cookie" style="display:none;">
            <div class="sw-cookie__inner">
                <div class="sw-cookie__text">
                    <strong>Мы используем куки</strong>
                    <p>Этот сайт использует куки для корректной работы и аналитики. Вы можете выбрать, какие куки разрешить.</p>
                </div>
                <div class="sw-cookie__options">
                    <label class="sw-cookie__option">
                        <input type="checkbox" checked disabled>
                        <span class="sw-cookie__check"></span>
                        <span>Обязательные (работа сайта)</span>
                    </label>
                    <label class="sw-cookie__option">
                        <input type="checkbox" id="sw-cookie-metrika">
                        <span class="sw-cookie__check"></span>
                        <span>Яндекс Метрика</span>
                    </label>
                </div>
                <div class="sw-cookie__actions">
                    <button class="sw-cookie__btn sw-cookie__btn--accept" id="sw-cookie-accept">Принять все</button>
                    <button class="sw-cookie__btn sw-cookie__btn--custom" id="sw-cookie-save">Принять выбранные</button>
                </div>
            </div>
        </div>
    </div>

    <div id="offcanvas" uk-offcanvas="overlay: true">
        <div class="uk-offcanvas-bar">
            <ul class="uk-nav uk-nav-default">
                <?php foreach ($menu as $item): ?>
                    <li>
                        <a href="<?= Html::encode(Url::to($item['url'])) ?>"><?= Html::encode($item['label']) ?></a>
                        <?php if (!empty($item['items'])): ?>
                            <ul class="uk-nav-sub">
                                <?php foreach ($item['items'] as $child): ?>
                                    <li>
                                        <a href="<?= Html::encode(Url::to($child['url'])) ?>"><?= Html::encode($child['label']) ?></a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <?php $this->endBody() ?>

    <?php if ((bool)$_ENV['ISADMINSCRIPT']): ?>
        <script type="text/javascript">
            window.SiteWidget = {
                apiUrl: '<?= $_ENV['DOMAINAPIWIDGET'] . '/api' ?>',
                staticUrl: '<?= $_ENV['DOMAINSTATICWIDGET'] ?>',
                customUrl: '<?= $_ENV['DOMAINCUSTOMWIDGET'] ?>',
                supportWsUrl: '<?= $_ENV['DOMAINWSWIDGET'] ?? '' ?>',
                publicKey: '<?= $_ENV['PK_WIDGET'] ?>',
                _user: {
                    <?php if (!is_null($id_user)): ?>
                    id: <?= $id_user ?>,
                    <?php else: ?>
                    id: null,
                    <?php endif; ?>
                    role: [<?= $str_role ?>],
                    name: <?= Json::htmlEncode($name_user ?: null) ?>,
                    email: <?= Json::htmlEncode($email_user ?: null) ?>
                }
            };

            var script = document.createElement('script');
            script.src = '<?= $_ENV['DOMAINSTATICWIDGET'] . '/lib.js' ?>', document.head.appendChild(script);
        </script>
    <?php endif; ?>

    </body>
    </html>
<?php $this->endPage() ?>
