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
$isOwner = false;

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
            ['label' => 'Модули', 'url' => '/#modules'],
            ['label' => 'Как работает', 'url' => '/#how'],
            ['label' => 'Интеграции', 'url' => '/#integrations'],
            ['label' => 'Приложение', 'url' => '/#android-app'],
            ['label' => 'Инструкции', 'url' => ['/instructions']],
            ['label' => 'Вход', 'url' => ['/login']],
    ];
} else {
    $email_user = Yii::$app->user->identity->email;
    $name_user = Yii::$app->user->identity->name;
    $id_user = Yii::$app->user->identity->id;
    $isOwner = (int)$id_user === (int)Yii::$app->user->identity->getPublicKey();
    $menu = [
            ['label' => 'Диалоги', 'url' => ['/manager/support/conversations']],
            ['label' => $isOwner ? 'Панель управления' : 'Личный кабинет', 'url' => [$isOwner ? '/manager' : '/manager/profile']],
            ['label' => 'Инструкции', 'url' => ['/instructions']],
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
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a href="/#modules">Модули</a>
                        <a href="/#how">Как работает</a>
                        <a href="/#integrations">Интеграции</a>
                        <a href="/#android-app">Приложение</a>
                        <a href="/instructions">Инструкции</a>
                    <?php else: ?>
                        <a href="/manager/support/conversations">Диалоги</a>
                        <a href="<?= $isOwner ? '/manager' : '/manager/profile' ?>"><?= $isOwner ? 'Панель управления' : 'Личный кабинет' ?></a>
                        <a href="/instructions">Инструкции</a>
                    <?php endif; ?>
                </nav>
                <div class="sw-header__actions">
                    <?php if (Yii::$app->user->isGuest): ?>
                        <a class="sw-header__login" href="/login">Войти</a>
                    <?php else: ?>
                        <a class="sw-header__user" href="/manager/profile"><?= Html::encode($name_user) ?></a>
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
                <div class="uk-grid-divider" uk-grid>
                    <?php if (isset($this->blocks['block_left_menu'])): ?>
                        <?= $this->blocks['block_left_menu'] ?>
                    <?php endif; ?>
                    <div class="<?= isset($this->blocks['block_left_menu']) ? 'uk-width-expand@s' : 'uk-width-1-1' ?>">
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
