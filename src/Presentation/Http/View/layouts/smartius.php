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

UikitAsset::register($this);
AppAsset::register($this);
CodemirrorAsset::register($this);

$email_user = '';
$name_user = '';
$id_user = null;

if (Yii::$app->user->isGuest) {
    $role = [1];
} else {
    $role = [2];
}
$role = Yii::$app->request->get()['roles'] ?? $role;
$testchatbots = Yii::$app->request->get()['testchatbots'] ?? 0;
$str_role = implode(",", $role);

if (Yii::$app->user->isGuest) {
    $menu = [
            ['label' => 'Вход', 'url' => ['/login']],
    ];
} else {
    $email_user = Yii::$app->user->identity->email;
    $name_user = Yii::$app->user->identity->name;
    $id_user = Yii::$app->user->identity->id;
    $menu = [
            ['label' => 'Выход', 'url' => ['/logout']],
    ];
}

$id_user = Yii::$app->request->get()['id_user'] ?? $id_user;
$request = Yii::$app->request;

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
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">
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
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="330 84 126 124" fill="none" class="sw-logo" width="69" height="69">
        <!-- Группа 1: верхняя дуга -->
        <g transform="matrix(1, 0, 0, 1, -1, 0)" class="sw-logo__piece">
            <path d="M399.571 117.961C399.571 102.031 410.412 91.412 423.907 91.412C437.403 91.412 448.244 102.031 448.244 117.961"
                  fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M399.571 117.961C399.571 114.2 402.447 111.324 406.208 111.324H409.527C411.297 111.324 412.845 112.872 412.845 114.642V127.916C412.845 129.686 411.297 131.235 409.527 131.235H406.208C402.447 131.235 399.571 128.359 399.571 124.598V117.961Z"
                  fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M448.244 117.961C448.244 114.2 445.367 111.324 441.606 111.324H438.288C436.518 111.324 434.969 112.872 434.969 114.642V127.916C434.969 129.686 436.518 131.235 438.288 131.235H441.606C445.367 131.235 448.244 128.359 448.244 124.598V117.961Z"
                  fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M434.969 133.447C432.314 136.324 428.553 137.872 423.907 137.872" fill="none" stroke="#2B245C"
                  stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="423.907" cy="137.872" r="3.54" fill="#2B245C"/>
        </g>
        <!-- Группа 2: два столбца с полосками -->
        <g transform="matrix(1, 0, 0, 1, 1, -0.102999)" class="sw-logo__piece">
            <path d="M339.131 96.723C339.131 93.807 341.423 91.515 344.339 91.515H359.964C362.256 91.515 364.131 93.39 364.131 95.682V139.432C364.131 137.14 362.256 135.265 359.964 135.265H344.339C341.423 135.265 339.131 132.973 339.131 130.057V96.723Z"
                  fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M389.131 96.723C389.131 93.807 386.839 91.515 383.923 91.515H368.298C366.006 91.515 364.131 93.39 364.131 95.682V139.432C364.131 137.14 366.006 135.265 368.298 135.265H383.923C386.839 135.265 389.131 132.973 389.131 130.057V96.723Z"
                  fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M347.464 104.015H356.839" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path d="M347.464 112.348H356.839" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path d="M347.464 120.682H354.756" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path d="M371.423 104.015H380.798" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path d="M371.423 112.348H380.798" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
            <path d="M371.423 120.682H378.714" fill="none" stroke="#2B245C" stroke-width="4.8" stroke-linecap="round"
                  stroke-linejoin="round"/>
        </g>
        <!-- Группа 3: чекбокс -->
        <g transform="matrix(1, 0, 0, 1, 0.987662, -0.81)" class="sw-logo__piece">
            <path d="M381.822 175.107L381.564 198.81H339.279V156.524L373.804 156.267" fill="none" stroke="#2B245C"
                  stroke-width="4.8" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M348.676 174.002L359.952 185.278L389.279 154.662" fill="none" stroke="#2B245C" stroke-width="4.8"
                  stroke-linecap="round" stroke-linejoin="round"/>
        </g>
        <!-- Группа 4: пазл -->
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
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="/#modules">Модули</a>
                        <a href="/#how">Как работает</a>
                        <a href="/#integrations">Интеграции</a>
                    <?php else: ?>
                        <a href="/manager">Панель управления</a>
                    <?php endif; ?>
                </nav>
                <div class="sw-header__actions">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a class="sw-header__login" href="/login">Войти</a>
                    <?php else: ?>
                        <span class="sw-header__user"><?= Html::encode($name_user) ?></span>
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
                <div class="uk-grid-divider uk-child-width-expand@s" uk-grid>
                    <?php if (isset($this->blocks['block_left_menu'])): ?>
                        <?= $this->blocks['block_left_menu'] ?>
                    <?php endif; ?>
                    <div class="uk-width-5-6@s">
                        <?= $content ?>
                    </div>
                </div>
            </div>
        </section>
        <?php if (empty($this->params['hideLayoutFooter'])): ?>
            <footer class="sw-footer uk-margin-top"">
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

    <?= Offcanvas::widget([
            'items' => $menu,
    ]) ?>

    <?php $this->endBody() ?>

    <?php if ((bool)$_ENV['ISADMINSCRIPT']): ?>
        <script type="text/javascript">
            window.Smartius = {
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
                    name: null,
                    email: null
                }
            };

            var script = document.createElement('script');
            script.src = '<?= $_ENV['DOMAINSTATICWIDGET'] . '/lib.js' ?>', document.head.appendChild(script);
        </script>
    <?php endif; ?>

    </body>
    </html>
<?php $this->endPage() ?>