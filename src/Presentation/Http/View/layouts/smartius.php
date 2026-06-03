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
$role = Yii::$app->request->get()['roles'] ?? $role;//?roles[]=1&roles[]=2
$testchatbots = Yii::$app->request->get()['testchatbots'] ?? 0;
$str_role = implode(",", $role);
if (Yii::$app->user->isGuest) {
    ///$role=1;
    $menu = [
        ['label' => 'Вход', 'url' => ['/login']],
    ];
} else {
    ///$role=2;
    $email_user = Yii::$app->user->identity->email;
    $name_user = Yii::$app->user->identity->name;
    $id_user = Yii::$app->user->identity->id;
    if (Yii::$app->user->can('manager') && !Yii::$app->user->can('admin')) {
        $menu = [
            ['label' => 'Выход', 'url' => ['/logout']],
        ];
    } else {
        $menu = [
            ['label' => 'Выход', 'url' => ['/logout']],
        ];
    }

}
$id_user = Yii::$app->request->get()['id_user'] ?? $id_user;//?id_user=82
$request = Yii::$app->request;

$this->beginPage();

?>

    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() // генерируем защитные токены для передачи POST , для проверки что данные были отправлены с нашего сайта      ?>
        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
        <meta name="yandex-verification" content="8404bd8126455b4e"/>
        <meta name="yandex-verification" content="56b4ceb0b5a35c1c"/>
    </head>
    <body>
    <div class="uk-offcanvas-content">

        <div class="tm-header uk-light uk-navbar-container tm-header-transparent"
             uk-sticky="top: 200; animation: uk-animation-slide-top; media: @m" uk-header>
            <div class="uk-container">
                <?php $this->beginBody();
                NavBar::begin([
                    'offcanvasTextMenu' => '',
                    'offcanvas' => 1,
                    'brandLabel' => '',
                    'brandUrl' => '/',
                    'idOffcanvas' => 'offcanvas',
                    'classNavBar' => 'uk-navbar-right  uk-visible@m',
                    'idNavBar' => 'navbarmy',
                ]);
                echo Nav::widget([
                    'items' => $menu, 'navbar' => true,
                ]);
                NavBar::end();
                ?>
            </div>
        </div>
        <div class="uk-container uk-margin">
            <!-- различные уведомления--><?= Alert::widget() ?>
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

        <div class="uk-container uk-margin"></div>
        <footer class="uk-section uk-section-primary">
            <div class="uk-container uk-container-small">
                <div class="uk-grid-divider uk-child-width-expand@s" uk-grid>
                    <?php
                    if ($_ENV['TYPE_DEPLOYED'] != 'MIRS') {
                    ?>
                        <div class="uk-text-center"><a href="mailto:mail@sitewidget.ru">mail@sitewidget.ru</a></div>
                        <div class="uk-text-center"></div>
                        <div class="uk-text-center">sitewidget.ru</div>
                        <div class="uk-text-center"><a href="/files/pzpd.docx">Политика конфиденциальности</a></div>
                    <?php
                    }

                    ?>
                </div>
            </div>
        </footer>

    </div>

    <?= Offcanvas::widget([
        'items' => $menu,
    ]);
    ?>
    <?php $this->endBody() ?>

    <?php

    $addtestchatbots = '';


    if ((bool)$_ENV['ISADMINSCRIPT']) {
    if ($testchatbots) {
        ?>
        <script type="text/javascript">
            window.open("//<?=$_ENV['DOMAINAPIWIDGET']?>/chatbottest.php?id=<?=$testchatbots?>&pk=<?=$_ENV['PK']?>");
            //win.focus();
        </script>
    <?php
    }
    ?>
        <script type="text/javascript">
            window.Smartius = {
                apiUrl: '<?=$_ENV['DOMAINAPIWIDGET'] . '/api'?>',
                staticUrl: '<?=$_ENV['DOMAINSTATICWIDGET']?>',
                customUrl: '<?=$_ENV['DOMAINCUSTOMWIDGET']?>',
                publicKey: '<?=$id_user > 1 ? $_ENV['PK_WIDGET'] : 3?>',
                _user: {
                    <?php
                    if (!is_null($id_user)) {
                        echo 'id:' . $id_user . ',';
                    } else {
                        echo 'id:null,';
                    }
                    ?>
                    role: [<?=$str_role?>],
                    name: null,
                    email: null
                }
            };

            var script = document.createElement('script');
            script.src = '<?=$_ENV['DOMAINSTATICWIDGET'] . '/lib.js'?>', document.head.appendChild(script);

        </script>

        <?php
    }
    ?>


    </body>
    </html>
<?php $this->endPage() ?>
