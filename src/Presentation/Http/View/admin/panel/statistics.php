<div class="uk-container uk-margin">
    <h3>Статистика</h3>


<?php
if(count($names)){
    $this->registerJsFile('js/charts.js');
}
//debug($names);
foreach($names as $key => $val){
    switch ($val) {
        case 'chart_api_configurations':
            $this->registerJs($js_chart_api_configurations);
            echo $html_chart_api_configurations;
            break;
        case 'chart_usage':
            $this->registerJs($js_chart_usage);
            echo $html_chart_usage;
            break;
        case 'chart_open_widget':
            $this->registerJs($js_chart_open_widget);
            echo $html_chart_open_widget;
            break;
        default;
    }
}


$this->title = "Главная панель управления";
?>



</div>
