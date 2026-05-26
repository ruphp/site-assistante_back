<?php

use yii\helpers\Html;
use yii\helpers\Url;

app\assets\AppAsset::register($this);
?>
<p>Вы действительно хотите удалить  роль?</p>
<button class="uk-button uk-button-default uk-modal-close" type="button">Отменить</button>
<?=  Html::a('Удалить', Url::to(['manager/role/delete', 'id' => $role['id']]), ['class' => 'uk-button uk-button-primary']);?>

