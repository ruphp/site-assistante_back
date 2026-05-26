<?php
/** @var $categories */
/** @var $series */
?>
/* не убирать эти 2 строки комментария !!!!!!!!!! слетит синтаксис JS в phpshtorm !!!!!!!!!!!!!
<script>*/

    let arr_colors_api_configurations = [

        <?php
        for($i=count($series); $i>0;--$i){
        ?>
        ' <?=sprintf('#%02X%02X%02X', rand(0, rand(1, 255)), rand(0, rand(1, 255)), rand(0,rand(1, 255))); ?>',


        <?php
        }
        ?>

    ];

    let arr_series_chart_api_configurations = [
        <?php  foreach ($series as $key=>$val ){?>
        {
            name: '<?=$val['name']?>',
            data: [<?=implode(',', $val['series'])?>]
        },
        <?php  }?>
    ];

    let categories_chart_api_configurations = <?="['" . implode("','", $categories) . "']"?>;

    const chart_api_configurations = Highcharts.chart('chart_api_configurations', {

        title: {
            text: null,
        },
        colors: arr_colors_api_configurations,

        yAxis: {
            title: {
                text: 'Кол-во обращений'
            }
        },

        xAxis: {
            categories: categories_chart_api_configurations,
        },

        series: arr_series_chart_api_configurations,

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });


    function setDateApiConfiguration(data) {

        //придет дата  - data


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

        chart_api_configurations.update({
            series: series ?? [],
            xAxis: {
                categories: categories ?? []
            },
        });


    }

    let date_from_chart_api_configurations = jQuery('input[name="from_date_chart_api_configurations"]');
    let date_from_val_chart_api_configurations = date_from_chart_api_configurations.val();
    let date_to_chart_api_configurations = jQuery('input[name="to_date_chart_api_configurations"]');
    let date_to_val_chart_api_configurations = date_to_chart_api_configurations.val();
    let only_unic_chart_api_configurations = jQuery('input[name="only_unic"]');
    let only_unic_val_chart_api_configurations = Number(only_unic_chart_api_configurations.is(':checked'));


    jQuery.get('/admin/statistics/chart?name=api_configurations&date_from=' + date_from_val_chart_api_configurations + '&date_to=' + date_to_val_chart_api_configurations + '&only_unic=' + only_unic_val_chart_api_configurations, function (data) {
        setDateApiConfiguration(data);
    });
    date_from_chart_api_configurations.on('change', () => {
        date_from_val_chart_api_configurations = date_from_chart_api_configurations[0].value;
        jQuery.get('/admin/statistics/chart?name=api_configurations&date_from=' + date_from_val_chart_api_configurations + '&date_to=' + date_to_val_chart_api_configurations + '&only_unic=' + only_unic_val_chart_api_configurations, function (data) {
            //console.log(data);
            setDateApiConfiguration(data);
        });
    });
    date_to_chart_api_configurations.on('change', () => {
        date_to_val_chart_api_configurations = date_to_chart_api_configurations[0].value;
        jQuery.get('/admin/statistics/chart?name=api_configurations&date_from=' + date_from_val_chart_api_configurations + '&date_to=' + date_to_val_chart_api_configurations + '&only_unic=' + only_unic_val_chart_api_configurations, function (data) {
            //console.log(data);
            setDateApiConfiguration(data);
        });
    });
    only_unic_chart_api_configurations.on('change', () => {
        only_unic_val_chart_api_configurations = Number(only_unic_chart_api_configurations.is(':checked'));
        jQuery.get('/admin/statistics/chart?name=api_configurations&date_from=' + date_from_val_chart_api_configurations + '&date_to=' + date_to_val_chart_api_configurations + '&only_unic=' + only_unic_val_chart_api_configurations, function (data) {
            //console.log(data);
            setDateApiConfiguration(data);
        });
    });