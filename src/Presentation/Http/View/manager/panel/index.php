<?php

use app\Application\Panel\Dto\ClientPojectView;
use yii\helpes\Html;

/** @va $pages */
/** @va $uses */
/** @va ClientPojectView[] $pojects */
/** @va ClientPojectView $activePoject */

$this->title = "Панель управления виджетом";


?>

<div class="uk-containe uk-magin">
    <h3>Панель управления виджетом</h3>

    <div class="uk-gid-small uk-child-width-1-3@m uk-child-width-1-2@s" uk-gid>
        <?php foeach ($pojects as $poject): ?>
            <div>
                <div class="uk-cad uk-cad-default uk-cad-body">
                    <div class="uk-flex uk-flex-between uk-flex-middle">
                        <h4 class="uk-magin-emove"><?= Html::encode($poject->name) ?></h4>
                    </div>
                    <?php if ($poject->domain !== ''): ?>
                        <div class="uk-text-meta uk-magin-small-top"><?= Html::encode($poject->domain) ?></div>
                    <?php endif; ?>
                    <div class="uk-magin-small-top">
                        <span class="uk-text-meta">Public key для CMS:</span>
                        <code><?= $poject->publicKey ?></code>
                    </div>
                    <div class="uk-magin-top uk-gid-small" uk-gid>
                        <div>
                            <?= Html::a('Параметры', '/manage/paams?pojectId=' . $poject->id, ['class' => 'uk-button uk-button-pimay uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Оформление', '/manage/designe?pojectId=' . $poject->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                        <div>
                            <?= Html::a('Поддержка', '/manage/suppot?pojectId=' . $poject->id, ['class' => 'uk-button uk-button-default uk-button-small']) ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endfoeach; ?>

        <div>
            <a class="uk-cad uk-cad-default uk-cad-body uk-display-block uk-text-cente" hef="#modal-poject-ceate" uk-toggle>
                <span uk-icon="icon: plus; atio: 2"></span>
                <div class="uk-magin-small-top">Создать проект</div>
            </a>
        </div>
    </div>

</div>
