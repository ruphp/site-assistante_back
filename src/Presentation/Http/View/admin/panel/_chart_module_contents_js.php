<?php
//debug($categories,1);
/** @var $categories */
/** @var $series */
?>
/* не убирать эти 2 строки комментария !!!!!!!!!! слетит синтаксис JS в phpshtorm !!!!!!!!!!!!!
<script>*/

    let arr_colors_module_contents = [

        <?php
        for($i=count($series); $i>0;--$i){
        ?>
        ' <?=sprintf('#%02X%02X%02X', rand(0, rand(1, 255)), rand(0, rand(1, 255)), rand(0, rand(1, 255))); ?>',


        <?php
        }
        ?>

    ];

    let arr_series_chart_module_contents = [
        <?php


        foreach ($series as $key=>$val ){?>
        {
            name: '<?=$val['name']?>',
            data: [<?=implode(',', $val['series'])?>]
        },
        <?php  }?>
    ];
    
    let categories_chart_module_contents = <?="['" . implode("','", $categories) . "']"?>;

    let chart_module_contents = Highcharts.chart('chart_module_contents', {
        chart: {
            type: 'column'
        },
        title: {
            text: null
        },
        xAxis: {
            categories: categories_chart_module_contents,
            crosshair: true,
            accessibility: {
                description: 'Countries'
            }
        },
        colors: arr_colors_module_contents,
        yAxis: {
            title: {
                text: 'Кол-во созданных элементов '
            }
        },
        plotOptions: {
            column: {
                pointPadding: 0.2,
                borderWidth: 0
            }
        },
        series: arr_series_chart_module_contents
    });


    let chart_module_contents_val_list_systems = 0;
    let chart_module_contents_list_systems = jQuery('#chart_module_contents_list_systems');
    if (chart_module_contents_list_systems.length > 0) {
        chart_module_contents_val_list_systems = chart_module_contents_list_systems ? chart_module_contents_list_systems.val() : 0;
    }

    function setDateContentStatistics(data) {
        //{
        // "categories":["тест"],
        // "series":[
        //      {"name":"База знаний","series":{"3":0}},
        //      {"name":"Всплывающие подсказки","series":{"3":0}},
        //      {"name":"Онбординги","series":{"3":0}},
        //      {"name":"Опросы","series":{"3":0}}
        // ]}

        let categories = []
        let series = [];

        $.each(data, function (index, value) {

            if (index === 'categories') {
                $.each(value, function (index2, value2) {
                    categories.push(value2);
                });
            }

            if (index === 'series') {
                $.each(value, function (index3, value3) {
                    let _series = [];
                    $.each(value3.series, function (index4, value4) {
                        _series.push(value4)
                    })
                    series.push({name: value3.name, data: _series});
                });

            }
        });

        chart_module_contents.update({
            series: series ?? [],
            xAxis: {
                categories: categories ?? []
            },
        });
    }

    chart_module_contents_list_systems.on('change', () => {

        chart_module_contents_val_list_systems = chart_module_contents_list_systems[0].value;

        jQuery.get('/admin/content_statistics/chart?name=module_contents&system=' + chart_module_contents_val_list_systems, function (data) {
            setDateContentStatistics(data);
        });
    });






