<?php
// работает через echo в конце


use app\Infrastructure\User\UserIdentity;
use app\Infrastructure\YiiActiveRecord\Users;
use yii\helpers\Html;


$users =  $clients=Users::getListUsersManager('public_key,firm');
//var_dump($users);exit();
$users_list = [];
foreach ($users as $user){
    $users_list[$user['public_key']]=$user['firm'];
}


$html = '<div class="uk-card uk-card-body uk-card-default stat_all">
            <h3 class="uk-card-title">Количество созданного контента по модулям ИС ЦИПП ПК</h3>
            <p>Количество созданных основных элементов у модулей</p>        
            <div uk-grid class="uk-child-width-1-2">
                <div class="uk-form-controls">';

if(count($users_list )) {
    $html .='<div>';
    $html .='<label class="control-label">Выбор системы</label>';

    $params = [
        'class'  => 'form-control uk-select',
        'id'  => 'chart_module_contents_list_systems'
    ];
    $html .='<div>';
    $html .= Html::dropDownList('id_system', null, $users_list , $params);
    $html .='</div>';
    $html .='</div>';
}

$html .= '</div>
  
            </div>

            <p></p>';

$html .= "<div id='chart_module_contents'></div>";
$html .= '</div>




';

echo $html;
