<?php
// работает через echo в конце

use kartik\date\DatePicker;

$start_date = $chart_filters['start_date'] ?? date("d-m-Y", strtotime("-6 days"));
$end_date = $chart_filters['end_date'] ?? date("d-m-Y");

$html = '<div class="uk-card uk-card-body uk-card-default stat_all">
        <h3 class="uk-card-title">Cтатистика интеграции виджета по подключенным системам</h3>
        <p>Сколько раз браузер интегрировал кнопку виджета на страницы систем</p>        
        
        
        <div uk-grid class="uk-child-width-1-2">


        <div>
    
    <div class="uk-form-controls">
    <label ><input name="only_unic" class="uk-checkbox uk-margin-remove" type="checkbox"> Учитывать только уникальные</label>
    </div>
  
    </div>
            <div>
            <label class="control-label">Выбор периода</label>
            <div class="smg_dat400">';

$html .= DatePicker::widget([
    'name'          => 'from_date_chart_api_configurations',
    'value'         => $start_date,
    'language'      => 'ru',
    'type'          => DatePicker::TYPE_RANGE,
    'name2'         => 'to_date_chart_api_configurations',
    'value2'        => $end_date,
    'separator'     => '<i class="glyphicon glyphicon-resize-horizontal"></i>',
    'pluginOptions' => [
        'autoclose' => true,
        'format'    => 'dd-mm-yyyy',
    ],
]);

$html .= '</div>
        </div>
    </div> 

    <p></p>';

$html .= "<div id='chart_api_configurations'></div>";
$html .= '</div>




';

echo $html;
