<?php
/** @var $categories */
/** @var $series */
//debug($series ,1);
?>
/* не убирать эти 2 строки комментария !!!!!!!!!! слетит синтаксис JS в phpshtorm !!!!!!!!!!!!!
<script>*/

    let arr_colors_usage = [

        <?php
            $colors=["#7cb5ec","#434348","#90ed7d","#f7a35c","#8085e9","#f15c80","#e4d354","#2b908f","#f45b5b"];
            shuffle($colors);
            for($i=0; $i<count($series);++$i){
        ?>
                '<?=$colors[0]; ?>',
                '<?=$colors[1]; ?>',
                '<?=$colors[2]; ?>',
                '<?=$colors[3]; ?>',
                '<?=$colors[4]; ?>',
                '<?=$colors[5]; ?>',
                '<?=$colors[6]; ?>',
                '<?=$colors[7]; ?>',
                '<?=$colors[8]; ?>',
        <?php
            }
        ?>

    ];
    let arr_series_chart_usage = [
        <?php

        foreach ($series as $key=>$val ){
        ?>
        {
            name: '<?=$val['name']?>',
            data: [<?=implode(',', $val['series'])?>]
        },
        <?php  }?>
    ];
    let categories_chart_usage = <?="['" . implode("','", $categories) . "']"?>;

    const chart_usage = Highcharts.chart('chart_usage', {

        title: {
            text: null,
        },


        yAxis: {
            title: {
                text: 'Кол-во открытий'
            }
        },

        xAxis: {
            categories: categories_chart_usage,
        },

        colors: arr_colors_usage,
        series: arr_series_chart_usage,

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


    function setDateUsage(data) {

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

        chart_usage.update({
            series: series ?? [],
            xAxis: {
                categories: categories ?? []
            },
        });


    }

    let date_from_chart_usage = jQuery('input[name="from_date_chart_usage"]');
    let date_from_val_chart_usage = date_from_chart_usage.val();

    let date_to_chart_usage = jQuery('input[name="to_date_chart_usage"]');
    let date_to_val_chart_usage = date_to_chart_usage.val();

    let only_unic_chart_usage = jQuery('input[name="usage_only_unic"]');
    let only_unic_val_chart_usage = Number(only_unic_chart_usage.is(':checked'));

    let chart_usage_group_period = jQuery('#chart_usage_group_period');
    let chart_usage_group_period_val= chart_usage_group_period.val();

    let chart_usage_val_list_roles = 0;
    let chart_usage_list_roles = jQuery('#chart_usage_list_roles');
    if (chart_usage_list_roles.length > 0) {
        chart_usage_val_list_roles = chart_usage_list_roles ? chart_usage_list_roles.val() : 0;
    }



    jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
        setDateUsage(data);
    });

    let chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
    jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);



    date_from_chart_usage.on('change', () => {

        date_from_val_chart_usage = date_from_chart_usage[0].value;

        jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
            setDateUsage(data);
        });

        chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
        jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);

    });
    date_to_chart_usage.on('change', () => {

        date_to_val_chart_usage = date_to_chart_usage[0].value;

        jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
            setDateUsage(data);
        });

        chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
        jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);

    });
    only_unic_chart_usage.on('change', () => {

        only_unic_val_chart_usage = Number(only_unic_chart_usage.is(':checked'));

        jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
            setDateUsage(data);
        });

        chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
        jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);

    });
    chart_usage_group_period.on('change', () => {
        chart_usage_group_period_val = chart_usage_group_period[0].value;
        jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
            setDateUsage(data);
        });

        chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
        jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);
    });
    chart_usage_list_roles.on('change', () => {

        chart_usage_val_list_roles = chart_usage_list_roles[0].value;

        jQuery.get('/api/report/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles, function (data) {
            setDateUsage(data);
        });

        chart_usage_link_xls = '/manager/export/xls/usage?date_from=' + date_from_val_chart_usage + '&date_to=' + date_to_val_chart_usage + '&usage_only_unic=' + only_unic_val_chart_usage + '&type_period=' + chart_usage_group_period_val + '&role=' + chart_usage_val_list_roles;
        jQuery('.chart_usage_link_xls').attr('href', chart_usage_link_xls);
    });


    var chart1 = chart_usage;
    var hide_show_all_button1 = $('#hide_show_all_series_button1');

    $('#chart_usage').on('click','.highcharts-legend-item',function(){
        var vis = 0;
        $(chart1.series).each(function(index,value){
            if (value.visible) {
                vis ++;
            }
        });
        if(chart1.series.length==vis){
            $('#hide_show_all_series_button1').addClass('hideall').removeClass('viewall');
            hide_show_all_button1.html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M2.9209 2.80859C3.31137 2.41812 3.94442 2.41822 4.33496 2.80859L6.93066 5.4043C8.33873 4.62521 10.0217 4.10059 12.001 4.10059C19.6802 4.10067 22.9004 12 22.9004 12C22.8836 12.0413 21.6486 15.0645 18.8848 17.3584L20.2959 18.7695C20.6864 19.1601 20.6864 19.7931 20.2959 20.1836C19.9054 20.574 19.2723 20.5741 18.8818 20.1836L2.9209 4.22266C2.53058 3.83217 2.53058 3.19908 2.9209 2.80859ZM5.98242 8.56641C5.71641 8.81567 5.39663 9.12149 5.16309 9.38281C4.39717 10.2398 3.83372 11.104 3.46289 11.7549C3.4134 11.8418 3.36741 11.925 3.3252 12.0029C3.36681 12.0802 3.41219 12.162 3.46094 12.248C3.82915 12.8982 4.38887 13.762 5.15234 14.6182C6.6812 16.3326 8.90506 17.8994 12.001 17.8994C13.0486 17.8994 13.9968 17.7198 14.8516 17.416L16.3545 18.9639C15.1029 19.5355 13.656 19.8994 12.001 19.8994C4.31645 19.8994 1.12288 12.0526 1.10156 12C1.10156 12 2.17213 9.37337 4.55078 7.14941L5.98242 8.56641ZM10.0068 12.1484C10.0787 13.1273 10.8551 13.9108 11.8311 13.9932L13.542 15.6924C13.0678 15.8905 12.547 16 12.001 16C9.79189 15.9999 8.00098 14.2091 8.00098 12C8.00098 11.4616 8.10793 10.9483 8.30078 10.4795L10.0068 12.1484ZM12.001 6.10059C10.6452 6.10059 9.45548 6.4043 8.41699 6.89062L10.04 8.51367C10.6195 8.187 11.2883 8.00002 12.001 8C14.2101 8 16.001 9.79086 16.001 12C16.001 12.7125 15.8129 13.3805 15.4863 13.96L17.4639 15.9375C17.9777 15.5261 18.4393 15.0783 18.8496 14.6182C19.6132 13.7619 20.1738 12.8982 20.542 12.248C20.5907 12.1621 20.6352 12.08 20.6768 12.0029C20.6346 11.925 20.5895 11.8417 20.54 11.7549C20.1692 11.104 19.6049 10.24 18.8389 9.38281C17.3037 7.6651 15.0797 6.10062 12.001 6.10059ZM12.001 10C11.8538 10 11.7104 10.0157 11.5723 10.0459L13.9541 12.4277C13.9842 12.2899 14.001 12.1469 14.001 12C14.001 10.8954 13.1055 10 12.001 10Z" fill="#626973"/></svg>');
        }
        if(vis == 0){
            $('#hide_show_all_series_button1').addClass('viewall').removeClass('hideall');
            hide_show_all_button1.html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.001 4.10156C19.6624 4.10165 22.8855 11.9645 22.9004 12.001C22.9004 12.001 19.7114 19.9004 12.001 19.9004C4.29061 19.9003 1.10156 12.001 1.10156 12.001C1.11255 11.9741 4.33494 4.10154 12.001 4.10156ZM12.001 6.10156C8.92241 6.10155 6.69827 7.66611 5.16309 9.38379C4.397 10.241 3.83372 11.1059 3.46289 11.7568C3.41346 11.8436 3.36736 11.9261 3.3252 12.0039C3.36684 12.0812 3.41214 12.1638 3.46094 12.25C3.82919 12.9001 4.38891 13.764 5.15234 14.6201C6.68119 16.3345 8.90512 17.9004 12.001 17.9004C15.0968 17.9004 17.3207 16.3345 18.8496 14.6201C19.6132 13.7638 20.1737 12.9002 20.542 12.25C20.5908 12.1639 20.6351 12.0812 20.6768 12.0039C20.6346 11.9262 20.5894 11.8435 20.54 11.7568C20.1692 11.1059 19.605 10.241 18.8389 9.38379C17.3037 7.66617 15.0795 6.1016 12.001 6.10156ZM12.001 8.00098C14.2101 8.00098 16.001 9.79184 16.001 12.001C16.001 14.2101 14.2101 16.001 12.001 16.001C9.79184 16.001 8.00098 14.2101 8.00098 12.001C8.00098 9.79184 9.79184 8.00098 12.001 8.00098ZM12.001 10.001C10.8964 10.001 10.001 10.8964 10.001 12.001C10.001 13.1055 10.8964 14.001 12.001 14.001C13.1055 14.001 14.001 13.1055 14.001 12.001C14.001 10.8964 13.1055 10.001 12.001 10.001Z" fill="#626973"/></svg>');

        }
    });

    $(document).on('click','#hide_show_all_series_button1.hideall',function(){
        $(chart1.series).each(function(){
            this.setVisible(false, false);
        });
        chart1.redraw();
        $('#hide_show_all_series_button1').addClass('viewall').removeClass('hideall');
        hide_show_all_button1.html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.001 4.10156C19.6624 4.10165 22.8855 11.9645 22.9004 12.001C22.9004 12.001 19.7114 19.9004 12.001 19.9004C4.29061 19.9003 1.10156 12.001 1.10156 12.001C1.11255 11.9741 4.33494 4.10154 12.001 4.10156ZM12.001 6.10156C8.92241 6.10155 6.69827 7.66611 5.16309 9.38379C4.397 10.241 3.83372 11.1059 3.46289 11.7568C3.41346 11.8436 3.36736 11.9261 3.3252 12.0039C3.36684 12.0812 3.41214 12.1638 3.46094 12.25C3.82919 12.9001 4.38891 13.764 5.15234 14.6201C6.68119 16.3345 8.90512 17.9004 12.001 17.9004C15.0968 17.9004 17.3207 16.3345 18.8496 14.6201C19.6132 13.7638 20.1737 12.9002 20.542 12.25C20.5908 12.1639 20.6351 12.0812 20.6768 12.0039C20.6346 11.9262 20.5894 11.8435 20.54 11.7568C20.1692 11.1059 19.605 10.241 18.8389 9.38379C17.3037 7.66617 15.0795 6.1016 12.001 6.10156ZM12.001 8.00098C14.2101 8.00098 16.001 9.79184 16.001 12.001C16.001 14.2101 14.2101 16.001 12.001 16.001C9.79184 16.001 8.00098 14.2101 8.00098 12.001C8.00098 9.79184 9.79184 8.00098 12.001 8.00098ZM12.001 10.001C10.8964 10.001 10.001 10.8964 10.001 12.001C10.001 13.1055 10.8964 14.001 12.001 14.001C13.1055 14.001 14.001 13.1055 14.001 12.001C14.001 10.8964 13.1055 10.001 12.001 10.001Z" fill="#626973"/> </svg>');

    });

    $(document).on('click','#hide_show_all_series_button1.viewall',function(){
        $(chart1.series).each(function(){
            this.setVisible(true, false);
        });
        chart1.redraw();
        $('#hide_show_all_series_button1').addClass('hideall').removeClass('viewall');
        hide_show_all_button1.html('<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"> <path d="M2.9209 2.80859C3.31137 2.41812 3.94442 2.41822 4.33496 2.80859L6.93066 5.4043C8.33873 4.62521 10.0217 4.10059 12.001 4.10059C19.6802 4.10067 22.9004 12 22.9004 12C22.8836 12.0413 21.6486 15.0645 18.8848 17.3584L20.2959 18.7695C20.6864 19.1601 20.6864 19.7931 20.2959 20.1836C19.9054 20.574 19.2723 20.5741 18.8818 20.1836L2.9209 4.22266C2.53058 3.83217 2.53058 3.19908 2.9209 2.80859ZM5.98242 8.56641C5.71641 8.81567 5.39663 9.12149 5.16309 9.38281C4.39717 10.2398 3.83372 11.104 3.46289 11.7549C3.4134 11.8418 3.36741 11.925 3.3252 12.0029C3.36681 12.0802 3.41219 12.162 3.46094 12.248C3.82915 12.8982 4.38887 13.762 5.15234 14.6182C6.6812 16.3326 8.90506 17.8994 12.001 17.8994C13.0486 17.8994 13.9968 17.7198 14.8516 17.416L16.3545 18.9639C15.1029 19.5355 13.656 19.8994 12.001 19.8994C4.31645 19.8994 1.12288 12.0526 1.10156 12C1.10156 12 2.17213 9.37337 4.55078 7.14941L5.98242 8.56641ZM10.0068 12.1484C10.0787 13.1273 10.8551 13.9108 11.8311 13.9932L13.542 15.6924C13.0678 15.8905 12.547 16 12.001 16C9.79189 15.9999 8.00098 14.2091 8.00098 12C8.00098 11.4616 8.10793 10.9483 8.30078 10.4795L10.0068 12.1484ZM12.001 6.10059C10.6452 6.10059 9.45548 6.4043 8.41699 6.89062L10.04 8.51367C10.6195 8.187 11.2883 8.00002 12.001 8C14.2101 8 16.001 9.79086 16.001 12C16.001 12.7125 15.8129 13.3805 15.4863 13.96L17.4639 15.9375C17.9777 15.5261 18.4393 15.0783 18.8496 14.6182C19.6132 13.7619 20.1738 12.8982 20.542 12.248C20.5907 12.1621 20.6352 12.08 20.6768 12.0029C20.6346 11.925 20.5895 11.8417 20.54 11.7549C20.1692 11.104 19.6049 10.24 18.8389 9.38281C17.3037 7.6651 15.0797 6.10062 12.001 6.10059ZM12.001 10C11.8538 10 11.7104 10.0157 11.5723 10.0459L13.9541 12.4277C13.9842 12.2899 14.001 12.1469 14.001 12C14.001 10.8954 13.1055 10 12.001 10Z" fill="#626973"/> </svg>');
    });


