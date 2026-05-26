<?php
/** @var $categories */
/** @var $series */
?>
/* не убирать эти 2 строки комментария !!!!!!!!!! слетит синтаксис JS в phpshtorm !!!!!!!!!!!!!
<script>*/

    let arr_colors_open_widget = [

        <?php
     for($i=count($series); $i>0;--$i){
    ?>
        ' <?=sprintf('#%02X%02X%02X', rand(0, rand(1, 255)), rand(0, rand(1, 255)), rand(0, rand(1, 255))); ?>',


    <?php
     }
            ?>

           ];



    let arr_series_chart_open_widget = [
        <?php

            foreach ($series as $key=>$val ){
        ?>
        {
            name: '<?=$val['name']?>',
            data: [<?=implode(',', $val['series'])?>]
        },
        <?php  }?>
    ];
    let categories_chart_open_widget = <?="['" . implode("','", $categories) . "']"?>;

    const chart_open_widget = Highcharts.chart('chart_open_widget', {

        title: {
            text: null,
        },


        yAxis: {
            title: {
                text: 'Кол-во обращений'
            }
        },

        xAxis: {
            categories: categories_chart_open_widget,
        },

        colors: arr_colors_open_widget,
        series: arr_series_chart_open_widget,

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


    function setDateOpenWidget(data) {

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

        chart_open_widget.update({
            series: series ?? [],
            xAxis: {
                categories: categories ?? []
            },
        });


    }

    let date_from_chart_open_widget = jQuery('input[name="from_date_chart_open_widget"]');
    let date_from_val_chart_open_widget = date_from_chart_open_widget.val();
    let date_to_chart_open_widget = jQuery('input[name="to_date_chart_open_widget"]');
    let date_to_val_chart_open_widget = date_to_chart_open_widget.val();
    let open_widget_only_unic_chart_open_widget = jQuery('input[name="open_widget_only_unic"]');
    let open_widget_only_unic_val_chart_open_widget = Number(open_widget_only_unic_chart_open_widget.is(':checked'));


    jQuery.get('/admin/statistics/chart?name=open_widget&date_from=' + date_from_val_chart_open_widget + '&date_to=' + date_to_val_chart_open_widget + '&open_widget_only_unic=' + open_widget_only_unic_val_chart_open_widget, function (data) {
        setDateOpenWidget(data);
    });
    date_from_chart_open_widget.on('change', () => {
        date_from_val_chart_open_widget = date_from_chart_open_widget[0].value;
        jQuery.get('/admin/statistics/chart?name=open_widget&date_from=' + date_from_val_chart_open_widget + '&date_to=' + date_to_val_chart_open_widget + '&open_widget_only_unic=' + open_widget_only_unic_val_chart_open_widget, function (data) {
            setDateOpenWidget(data);
        });
    });
    date_to_chart_open_widget.on('change', () => {
        date_to_val_chart_open_widget = date_to_chart_open_widget[0].value;
        jQuery.get('/admin/statistics/chart?name=open_widget&date_from=' + date_from_val_chart_open_widget + '&date_to=' + date_to_val_chart_open_widget + '&open_widget_only_unic=' + open_widget_only_unic_val_chart_open_widget, function (data) {
            setDateOpenWidget(data);
        });
    });
    open_widget_only_unic_chart_open_widget.on('change', () => {
        open_widget_only_unic_val_chart_open_widget = Number(open_widget_only_unic_chart_open_widget.is(':checked'));
        jQuery.get('/admin/statistics/chart?name=open_widget&date_from=' + date_from_val_chart_open_widget + '&date_to=' + date_to_val_chart_open_widget + '&open_widget_only_unic=' + open_widget_only_unic_val_chart_open_widget, function (data) {
            setDateOpenWidget(data);
        });
    });
