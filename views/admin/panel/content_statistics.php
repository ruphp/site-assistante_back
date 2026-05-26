<div class="uk-container uk-margin">
    <h3>Статистика</h3>


<?php
if(count($names)){
    $this->registerJsFile('js/charts.js');
}
//debug($names);
foreach($names as $key => $val){
    switch ($val) {
        case 'chart_module_contents':
            $this->registerJs($js_chart_module_contents);
            echo $html_chart_module_contents;
            break;
        default;
    }
}


$this->title = "Главная панель управления";
?>



</div>
