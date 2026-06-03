<div class="uk-container uk-margin">
    <h3>Отчеты и аналитические панели</h3>
<?php

/** @var $names */
/** @var $js_chart_usage */
/** @var $html_chart_usage */

$this->title = "Панель управления виджетом - Отчеты";

if (count($names)) {
    $this->registerJsFile('../js/charts.js');
}
//debug($names);
foreach ($names as $key => $val) {

            //debug(1,1);
            $this->registerJs($js_chart[$val]);
            echo $html_chart[$val];


}

?>


</div>