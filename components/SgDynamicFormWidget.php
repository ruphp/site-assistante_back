<?php

namespace app\components;

use ruwmapps\dynamicform\DynamicFormAsset;
use ruwmapps\dynamicform\DynamicFormWidget;

class SgDynamicFormWidget extends DynamicFormWidget
{
    public $view_warning_edit = 0;// вывод оповещения при изменении

    public $type = 'default'; // тип

    public $parentContainer;

    private $is_edit_survey_for_poll = false; // признак вопросов

    private $is_edit_variant_for_poll = false; // признак вариантов

    private function addmodal($formId, $parentContainer, $el)
    {
        $modal = '
        //console.log( "'.$parentContainer.'");
        jQuery("#' . $formId . '").on("click", ".' . $parentContainer . '[data-is-log=\'1\'] ' . $el . '",          
        function(e) {
                    //console.log( "'.$el.'");
                    e.target.blur();
                    e.preventDefault();   
                    var m =  UIkit.modal;
                    m.labels = {
                        ok: "Продолжить",
                        cancel:"Отменить"
                    };
                    m.confirm("Внимание ! <br\> Изменение в уже запущенной опросе имеющей ответы, после их сохранения, очистит текущие отчеты по ней!")
                    .then(function () {
                        jQuery(".' . $parentContainer . '").attr("data-is-log",0); 
        }, function () {
                });
            });';
        return $modal;
    }

    public function registerAssets($view)
    {
        if($this->type == 'survey'){
            $this->is_edit_survey_for_poll = true;
        }
        if($this->type == 'variant'){
            $this->is_edit_variant_for_poll = true;
        }


        DynamicFormAsset::register($view);
        $js = '';
        // зададим доп скрипты для редактирования.удаления ответов анкет
        if ($this->is_edit_survey_for_poll) {
            $js_add_point_event = "  
                //console.log('addpoint');
                count_point = 0;
                var parent = $(this_edit_button_point).parent();
                /////////////
                parent.find('.point-item').each(function (index) {
                    $(this).find('.bage-point').html('Вопрос ' + (index + 1));
                    count_point++;
                    $(this).find('.container-shags').attr('datapoint', (index + 1));
                    $(this).find('.remove-point').show();
                    count_shag = 0;
                    $(this).find('.shag-item').each(function (index2) {
                        $(this).find('.bage-shag').html('Вариант ' + (index2 + 1));
                        count_shag++;
                        $(this).find('.remove-shag').show();
                        $(this).find('.remove-shag').attr('dataparentpoint', (index + 1));
                    });
                    if (count_shag == 5) {
                        $(this).find('.add-shag').hide();
                    }
                    if (count_shag == 1) {
                        $(this).find('.remove-shag').hide();
                    }
                
                    // проверим типы вопросов
                    var type = $('input[name=\"Surveys[' + index + '][type]\"]:checked').val() * 1;
                    if (type == 2) {
                        $(this).find('.uk-card').show();
                        $(this).find('.free').hide();
                    } else if (type == 3) {
                        $(this).find('.uk-card').show();
                        $(this).find('.free').show();
                
                    } else {
                        $(this).find('.uk-card').hide();
                        $(this).find('.free').hide();
                    }
                
                
                });
                parent.find('.point-item').last().find('input[type=\"radio\"][value=1]').prop('checked', true);
                
                //////////////////////////
                
                
                // position = right
                
                parent.find('.point-item').last().find('.bage-shag').html('Вариант 1');
                parent.find('.point-item').last().find('.remove-shag').hide();
                
                
                if (count_point == 20) {
                    $('.add-point').hide();
                } 
            ";
            $js_del_point_event = "
                count_point=0;
    setTimeout(function(){
        $('.dynamicform_wrapper .container-items').children().each(function( index ) {  
            ////console.log(index);
            $('.container-items .point-item:nth-child(' +(index+1)+ ')').find('.bage-point').html('Вопрос '+ (index+1));
            count_point = ( index+1 );        
            $(this).find('.container-shags').attr('datapoint',(index + 1));
            count_shag = 0;
            $(this).find('.shag-item' ).each(function( index2 ) {    
                $(this).find('.bage-shag').html('Вариант '+ (index2 + 1));
                count_shag ++;
                $(this).find( '.remove-shag' ).show();           
                $(this).find( '.remove-shag' ).attr('dataparentpoint',(index + 1));             
            });  
            if(count_shag == 5){
                 $(this).find('.add-shag').hide();
            }  
            if(count_shag == 1){
                 $(this).find('.remove-shag').hide();  
            } 
        });
        if(count_point==1){
            $( '.remove-point' ).hide();
        }
        if(count_point<20){
            $('.add-point').show();
        }
    }, 100);   
            ";
        }
        // зададим доп скрипты для редактирования.удаления ответов анкет
        elseif ($this->is_edit_variant_for_poll) {
            $js_add_variant_event = "  
                    //console.log('addvar');  
                    var dataparentpoint = $(this_edit_button_variant).prev().attr('datapoint');
                    var butaddshag = $(this_edit_button_variant);
                    var parent = $(this_edit_button_variant).parent();
                    count_shag = 0;
                    parent.find('.shag-item' ).each(function( index ) {    
                        $(this).find('.bage-shag').html('Вариант '+ (index + 1));
                        count_shag ++;
                        $(this).find( '.remove-shag' ).show();           
                        $(this).find( '.remove-shag' ).attr('dataparentpoint',dataparentpoint);
                        if(count_shag == 5){
                           butaddshag.hide();
                        }   
                    });  
            ";
            $js_del_variant_event = "
    var dataparentpoint = $(this_delete_button_variant).attr('dataparentpoint');
    //console.log('rem_var');
    count_shag = 0;
    var parent = $('[datapoint=\"'+dataparentpoint+'\"]');
    setTimeout(function(){
        parent.find('.shag-item' ).each(function( index ) {    
            $(this).find('.bage-shag').html('Вариант '+ (index + 1));
            count_shag ++;
        });
        if(count_shag == 1){
           parent.find( '.remove-shag' ).hide();
        }   
    }, 100); 
            ";
        }

        // зададим форме атрибут для определения поведения
        if ($this->view_warning_edit && $this->is_edit_survey_for_poll) {
            $js .= 'jQuery(".' . $this->widgetContainer . '").attr("data-is-log",1);';
        }
        elseif($this->is_edit_survey_for_poll) {
            $js .= 'jQuery(".' . $this->widgetContainer . '").attr("data-is-log",0);';
        }




        if ($this->is_edit_survey_for_poll) {
///////////// слушатель check.free , для формы с уведомлением
            $js .= $this->addmodal($this->formId, $this->widgetContainer, '.free');

///////////// слушатель radio.type_survey , для формы с уведомлением
            $js .= $this->addmodal($this->formId, $this->widgetContainer, '.type_survey');

///////////// слушатель input.read-edit , для формы с уведомлением
///
            $js .= $this->addmodal($this->formId, $this->widgetContainer, 'input.read-edit');
        }

        elseif ($this->is_edit_variant_for_poll) {

///////////// слушатель input.read-edit , для формы с уведомлением
///
            $js .= $this->addmodal($this->formId, $this->parentContainer, 'input.read-edit2');
        }




///////////// слушатель кнопки add , для формы с уведомлением
        $js .= '         var ee;
                        var this_edit_button_point;
                        var this_edit_button_variant;
        jQuery("#' . $this->formId . '").on("click", ".' . $this->parentContainer . '[data-is-log=\'1\'] ' . $this->insertButton . '", function(e) {' . "\n";

        // для вопроса анкеты
        if ($this->is_edit_survey_for_poll) {
            // показываем предупреждение
            $js .= 'this_edit_button_point = this;';
            $js .= '   e.target.blur();
                           e.preventDefault();                       
                            ee = e;
                            var m =  UIkit.modal;
                            m.labels = {
            ok: "Продолжить",
            cancel:"Отменить"
        };
                            m.confirm("Внимание ! <br\> Изменение в уже запущенном опросе имеющей ответы, после их сохранения, очистит текущие отчеты по ней!").then(function () {
                            
                           ';
            $js .= '       jQuery(".' . $this->widgetContainer . '").triggerHandler("beforeInsert", [jQuery(this_edit_button_point)]);';
            $js .= '      jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("addItem", ' . parent::getHashVarName() . ', ee, jQuery(this_edit_button_point));';
            $js .= $js_add_point_event;
            $js .= 'jQuery(".' . $this->parentContainer . '").attr("data-is-log",0);';

            $js .= ' }, function () {
                 
               
               });';

        }
        // для варианта ответа из вопроса
        elseif ($this->is_edit_variant_for_poll) {

            $js .= "//console.log(534645364564);";
            $js .= 'this_edit_button_variant = this;';
            $js .= '   e.target.blur();
                           e.preventDefault();                       
                            ee = e;
                            var m =  UIkit.modal;
                            m.labels = {
            ok: "Продолжить",
            cancel:"Отменить"
        };
                            m.confirm("Внимание ! <br\> Изменение в уже запущенном опросе имеющей ответы, после их сохранения, очистит текущие отчеты по ней!").then(function () {
                            
                           ';
            $js .= '       jQuery(".' . $this->widgetContainer . '").triggerHandler("beforeInsert", [jQuery(this_edit_button_variant)]);';
            $js .= '      jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("addItem", ' . parent::getHashVarName() . ', ee, jQuery(this_edit_button_variant));';
            $js .= $js_add_variant_event;
            $js .= 'jQuery(".' . $this->parentContainer . '").attr("data-is-log",0);';

            $js .= ' }, function () {
                 
               
               });';
        }
        // для остального
        else {
            $js .= "    e.preventDefault();\n";
            $js .= '    jQuery(".' . $this->widgetContainer . '").triggerHandler("beforeInsert", [jQuery(this)]);';
            $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("addItem", ' . parent::getHashVarName() . ', e, jQuery(this));';
        }

        $js .= "});\n";

///////////// слушатель кнопки add , для формы  без уведомления

        $js .= 'jQuery("#' . $this->formId . '").on("click", ".' . $this->parentContainer . '[data-is-log=\'0\'] ' . $this->insertButton . '", function(e) {' . "\n";
        $js .= "    e.preventDefault();\n";
        $js .= '    jQuery(".' . $this->widgetContainer . '").triggerHandler("beforeInsert", [jQuery(this)]);';
        $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("addItem", ' . parent::getHashVarName() . ', e, jQuery(this));';

        // запуск доп скриптов для вопроса анкеты
        if ($this->is_edit_survey_for_poll) {
            // показываем предупреждение
            $js .= 'this_edit_button_point = this;';
            $js .= $js_add_point_event;
        }
        // запуск доп скриптов для ваврианта
        elseif ($this->is_edit_variant_for_poll) {
            // показываем предупреждение
            $js .= 'this_edit_button_variant = this;';
            $js .= $js_add_variant_event;
        }
        $js .= "});\n";

        $view->registerJs($js, $view::POS_READY);
/////////  end insert



///////////// слушатель кнопки del , для формы с уведомлением/////////////////////////////
        $js = 'var ed;
         var this_delete_button_point;
         var this_delete_button_variant;
        jQuery("#' . $this->formId . '").on("click", ".' . $this->parentContainer . '[data-is-log=\'1\'] ' . $this->deleteButton . '", function(e) {  ' . "\n";

        // для вопроса анкеты при явном указании, с выводом модала
        if ($this->is_edit_survey_for_poll) {
            // показываем предупреждение

            $js .= '//console.log("del1"); this_delete_button_point = this;';
            $js .= '   
                           e.target.blur();
                           e.preventDefault();     
                           ed = e; 
                            var m =  UIkit.modal;
                            m.labels = {
                                ok: "Продолжить",
                                cancel:"Отменить"
                            };
                            m.confirm("Внимание ! <br\> Изменение в уже запущенном опросе имеющей ответы, после их сохранения, очистит текущие отчеты по ней!").then(function () {
                            
                           ';

            $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("deleteItem", ' . parent::getHashVarName() . ",ed, jQuery(this_delete_button_point));\n";
            $js .= $js_del_point_event;
            $js .= 'jQuery(".' . $this->parentContainer . '").attr("data-is-log",0);';
            $js .= ' }, function () {
                 
               
               });';

        }

        // для варианта при явном указании, с выводом модала
        elseif ($this->is_edit_variant_for_poll) {
            // показываем предупреждение

            $js .= '//console.log("del2"); this_delete_button_variant = this;';
            $js .= '   
   e.target.blur();
                           e.preventDefault();     
                           ed = e; 
                            var m =  UIkit.modal;
                            m.labels = {
                                ok: "Продолжить",
                                cancel:"Отменить"
                            };
                            m.confirm("Внимание ! <br\> Изменение в уже запущенном опросе имеющей ответы, после их сохранения, очистит текущие отчеты по ней!").then(function () {
                            
                           ';

            $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("deleteItem", ' . parent::getHashVarName() . ",ed, jQuery(this_delete_button_variant));\n";
            $js .= $js_del_variant_event;
            $js .= 'jQuery(".' . $this->parentContainer . '").attr("data-is-log",0);';
            $js .= ' }, function () {
                 
               
               });';

        }

        // для остального
        else {
            $js .= " //console.log('del3');   e.preventDefault();\n";
            $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("deleteItem", ' . parent::getHashVarName() . ", e, jQuery(this));\n";
        }
        $js .= "});\n";

        ///////////// слушатель кнопки del , для формы  без уведомления

        $js .= 'jQuery("#' . $this->formId . '").on("click", ".' . $this->parentContainer . '[data-is-log=\'0\'] ' . $this->deleteButton . '", function(e) {' . "\n";
        $js .= "    e.preventDefault();\n";
        $js .= '    jQuery(".' . $this->widgetContainer . '").yiiDynamicForm("deleteItem", ' . parent::getHashVarName() . ", e, jQuery(this));\n";

        // запуск доп скриптов для вопроса анкеты
        if ($this->is_edit_survey_for_poll) {
            // показываем предупреждение
            $js .= 'this_del_button = this;';
            $js .= $js_del_point_event;
        }
        $js .= "});\n";

        $view->registerJs($js, $view::POS_READY);
/////////  end remove
///
///
/// hz
        $js = 'jQuery("#' . $this->formId . '").yiiDynamicForm(' . parent::getHashVarName() . ');' . "\n";
        $view->registerJs($js, $view::POS_LOAD);
    }
}