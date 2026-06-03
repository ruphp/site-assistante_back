<?php
// работает через echo в конце

use app\Infrastructure\YiiActiveRecord\Roles;
use app\Infrastructure\YiiActiveRecord\Users;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

$users =  $clients=Users::getListUsersManager('public_key,firm');
//var_dump($users);exit();
$users_list = [];
foreach ($users as $user){
    $users_list[$user['public_key']]=$user['firm'];
}

$start_date = $chart_filters['start_date'] ?? date("d-m-Y", strtotime("-6 days"));
$end_date = $chart_filters['end_date'] ?? date("d-m-Y");
$system = $chart_filters['system'] ?? 0;
$html = '
<div class="uk-card uk-card-body uk-card-default stat_all">
    <h3 class="uk-card-title">Суммарные показатели по количеству пользователей, использующих модули ИС ЦИПП ПК</h3> 
    <div uk-grid class="uk-child-width-1-2 uk-margin">   
        <div>';
if(count($users_list )) {
    $html .='<div>';
    $html .='<label class="control-label">Выбор системы</label>';

    $params = [
        'class'  => 'form-control uk-select',
        'id'  => 'chart_usage_list_systems'
    ];
    $html .='<div>';
    $html .= Html::dropDownList('id_system', null, $users_list , $params);
    $html .='</div>';
    $html .='</div>';
}

$html .= '</div>

        <div>
            <label class="control-label">Выбор периода</label>
            <div class="smg_dat400">';

                $html .= DatePicker::widget([
                    'name'          => 'from_date_chart_usage',
                    'value'         => $start_date,
                    'language'      => 'ru',
                    'type'          => DatePicker::TYPE_RANGE,
                    'name2'         => 'to_date_chart_usage',
                    'value2'        => $end_date,
                    'separator'     => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
                    'pluginOptions' => [
                        'autoclose' => true,
                        'format'    => 'dd-mm-yyyy',
                    ],
                ]);

                $html .= '
            </div>
        </div>
         <div>
            <label class="control-label">Группировка результатов</label>
             <div>
                <select id="chart_usage_group_period" class="form-control" name="type_period" value="0">
                    <option value="day">По дням</option>
                    <option value="week">По неделям</option>
                    <option value="month">По месяцам</option>
                    <option value="quart">По кварталам</option>
                    <option value="year">По годам</option>
                </select>
            </div>
        </div>
        <div>           
            <div class="uk-form-controls">
                <label>
                    <input name="usage_only_unic" class="uk-checkbox uk-margin-remove" type="checkbox"> Учитывать только уникальные
                </label>
            </div>  
        </div>
         
    </div> ';

$html .=
    "<div id='chart_usage' class='uk-margin-none'></div>
</div>";

echo $html;

