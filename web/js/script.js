
$("#checkAll").on('click',function(){
    $(".listCheckbox input").prop('checked', $(this).prop('checked'));
});

// Снимаем отметки со всех чекбоксов
$("#uncheckAll").on('click',function(e){

    e.preventDefault();
    $(".listCheckbox input").prop('checked', false);
    $("#checkAll").prop('checked', false);
});
// Снимаем отметки со всех чекбоксов


// Если все чекбоксы отмечены, отмечаем "Отметить все"
$(".listCheckbox input").on('change',function(){

    if ($('.listCheckbox input:checked').length == $('.listCheckbox input').length) {
        $("#checkAll").prop('checked', true);
    } else {
        $("#checkAll").prop('checked', false);
    }
});
//////////////////////////////////////////////////////
$(".checkAll2").on('click',function(){
    let parent = $(this).closest('.uk-open');
    parent.find(".listCheckbox2 input").prop('checked', $(this).prop('checked'));
});

// Снимаем отметки со всех чекбоксов
$(".uncheckAll2").on('click',function(e){
    let parent = $(this).closest('.uk-open');
    parent.find(".listCheckbox2 input").prop('checked', false);
    parent.find(".checkAll2").prop('checked', false);
});
// Снимаем отметки со всех чекбоксов


// Если все чекбоксы отмечены, отмечаем "Отметить все"
$(".listCheckbox2 input").on('change',function(){
    let parent = $(this).closest('.uk-open');
    if (parent.find('.listCheckbox2 input:checked').length == parent.find('.listCheckbox2 input').length) {
        parent.find(".checkAll2").prop('checked', true);
    } else {
        parent.find(".checkAll2").prop('checked', false);
    }
});

jQuery(document).ready(function () {

    var type = 10;
    type = $('input[name="Points[type]"]:checked').val();
    if (type == 1) {
        $(".field-select_list").show();
        $(".field-points-autostart").show();
        $(".field-points-content").hide();
    } else if (type == 0) {
        $(".field-points-content").show();
    }else if (type == 2) {
        $(".field-select_list").hide();
        $(".field-points-autostart").hide();
        $(".field-points-content").show();
    }
    //$(".field-select_list").hide();
// переключатель радиокнопок типов ТП в параметрах
    radioTpParam(jQuery('#params-tab_tickets input:radio:checked').val());
    jQuery('#params-tab_tickets input:radio').change(function () {
        radioTpParam(jQuery(this).val());
    });


    $('.modal-point').on('click', function () {
        var point = $(this).attr("data-point");
//console.log(point);
        var type = $("#modal-edit-point" + point).find('input[name="Points[type]"]:checked').val();
        if (point == 0) {
            type = $("#modal-new-point").find('input[name="Points[type]"]:checked').val();
            point = '';
        }
//console.log(point);
//console.log(type);
        if (type == 1) {
            $(".field-select_list").show();
            $(".field-points-autostart").show();
            $(".field-points-content").hide();
        } else if (type == 0) {
            $(".field-select_list").show();
            $(".field-points-autostart").show();
            $(".field-points-content").show();
        }
    });
    $('input[name="Points[type]"]').change(function () {
        var point = $(this).parents("#points-type").attr("data-point");
        //console.log(point);
        if (point == undefined) {
            point = '';
        }
        var type = $(this).val();
        if (type == 1) {
            $(".field-select_list").show();
            $(".field-points-autostart").show();
            $(".field-points-content").hide();
        } else if (type == 0 ) {
            $(".field-select_list").show();
            $(".field-points-autostart").show();
            $(".field-points-content").show();
        }else if (type == 2) {
            $(".field-select_list").hide();
            $(".field-points-autostart").hide();
            $(".field-points-content").show();
        }
    });

// скрипты для страницы микрокурсов
    var GETArr = parseGetParams();
    var myParamcat = GETArr.cat;
    if (myParamcat === undefined) {
        myParamcat = -1;
    }
    var myParamrole = GETArr.role;
    if (myParamrole === undefined) {
        myParamrole = -1;
    }
    $('#catfilter').val(myParamcat).prop('selected', true);
    $('#rolefilter').val(myParamrole).prop('selected', true);
    $('.adminpoint_a').click(function (e) {
        e.preventDefault();
        console.log(123);
    });

    $("#priceform-phone").mask("+7 (999) 999-99-99");



    $('.field-questions-0-is_first').next().addClass('uk-hidden');
    $('.field-questions-0-question').addClass('uk-hidden');
    $('input#questions-0-question').val('В начало');
    $('.field-questions-0-otvet>label').text('Приветственное сообщение');
    $('input.is_first:checkbox').prop('checked', false);
    $('#questions-0-is_first:checkbox').prop('checked', true);
});
/*$.when(
    $.getJSON("/api/is-event", { id_cat: $('#helpposts-id_cat').val() })
).done( function(json) {
    if(json.event){
        $('.field-helpposts-status').show();
    }else{
        $('.field-helpposts-status').hide();
    }
});*/
jQuery(document).on('change', '#testForm .spectrum-input', function () {
    console.log('12356');
    $color = $(this).val();
    console.log($color);
    $('.adminpoint_div').css('border-color', $color);
    $('.fonimage').children().css('fill', $color);
});
jQuery(document).on('change', 'input[name="Params[centerbutton]"]', function () {
    console.log($(this).val());
    if ($(this).val() > 0) {//вкл
        jQuery('.params-topbutton').removeClass('uk-hidden');
    } else {//вsкл
        jQuery('.params-topbutton').addClass('uk-hidden');
    }
});
jQuery(document).on('change', 'input[name="Params[centerbutton2]"]', function () {
    console.log($(this).val());
    if ($(this).val() > 0) {//вкл
        jQuery('.params-leftbutton').removeClass('uk-hidden');
    } else {//вsкл
        jQuery('.params-leftbutton').addClass('uk-hidden');
    }
});
jQuery(document).on('keyup', '#testForm .spectrum-input', function () {
    console.log('123');
    $color = $(this).val();
    $('.adminpoint_div').css('border-color', $color);
    $('.fonimage').children().css('fill', $color);
});
jQuery(document).on('change', '#helpposts-id_cat', function () {

    jQuery.when(
        jQuery.getJSON("/api/is-event", {id_cat: $(this).val()})
    ).done(function (json) {
        if (json.event) {
            jQuery('.posts-status').show();
        } else {
            jQuery('.posts-status').hide();
        }
    });
});

function parseGetParams() {
    var $_GET = {};
    var __GET = window.location.search.substring(1).split("&");
    for (var i = 0; i < __GET.length; i++) {
        var getVar = __GET[i].split("=");
        $_GET[getVar[0]] = typeof (getVar[1]) == "undefined" ? "" : getVar[1];
    }
    return $_GET;
}

function catfilter(sel) {
//console.log(sel.options[sel.selectedIndex].value);

    document.location.href = document.location.origin + document.location.pathname + '?';
    if (sel.options[sel.selectedIndex].value >= 0 && document.getElementById('rolefilter').value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?cat=' + sel.options[sel.selectedIndex].value + '&role=' + document.getElementById('rolefilter').value;
    } else if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?cat=' + sel.options[sel.selectedIndex].value;
    } else if (document.getElementById('rolefilter').value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + document.getElementById('rolefilter').value;
    }
}

function rolefilter(sel) {
    document.location.href = document.location.origin + document.location.pathname + '';
    if (sel.options[sel.selectedIndex].value >= 0 && document.getElementById('catfilter').value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value + '&cat=' + document.getElementById('catfilter').value;
    } else if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value;
    } else if (document.getElementById('catfilter').value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?cat=' + document.getElementById('catfilter').value;
    }
}

function rolefilterhint(sel) {
    document.location.href = document.location.origin + document.location.pathname + '';
    if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value;
    }
}

function rolefilterchatbot(sel) {
    document.location.href = document.location.origin + document.location.pathname + '';
    if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value;
    }
}

function rolefiltersurvey(sel) {
    document.location.href = document.location.origin + document.location.pathname + '';
    if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value;
    }
}

function rolefiltertema(sel) {
    document.location.href = document.location.origin + document.location.pathname + '';
    if (sel.options[sel.selectedIndex].value >= 0) {
        document.location.href = document.location.origin + document.location.pathname + '?role=' + sel.options[sel.selectedIndex].value;
    }
}


function radioTpParam(sel) {
    if (sel === '0') {
        jQuery('#login_sui').hide();
        jQuery('#parol_sui').hide();
        jQuery('#server_sui').hide();
        jQuery('#token_stp').hide();
        jQuery('#server_stp').hide();
    }
    if (sel === '1') {
        jQuery('#login_sui').hide();
        jQuery('#parol_sui').hide();
        jQuery('#server_sui').hide();
        jQuery('#token_stp').show();
        jQuery('#server_stp').show();
    }
    if (sel === '2') {
        jQuery('#login_sui').show();
        jQuery('#parol_sui').show();
        jQuery('#server_sui').show();
        jQuery('#token_stp').hide();
        jQuery('#server_stp').hide();
    }
}


function validateMyForm(inpName, inpEmail) {
    $(".text-error").remove();
    // Проверка логина
    var el_l = inpName;
    console.log(el_l.val());
    if (el_l.val().length < 1) {
        var v_login = true;
        el_l.next().html('<span class="text-error for-login">Заполните ФИО</span>');
        el_l.addClass('uk-form-danger');
    } else {
        el_l.removeClass('uk-form-danger');
    }

    // Проверка e-mail

    var reg = /^\w+([\.-]?\w+)*@(((([a-z0-9]{2,})|([a-z0-9][-][a-z0-9]+))[\.][a-z0-9])|([a-z0-9]+[-]?))+[a-z0-9]+\.([a-z]{2}|(com|net|org|edu|int|mil|gov|arpa|biz|aero|name|coop|info|pro|museum))$/i;

    var el_e = inpEmail;
    var v_email = el_e.val() ? false : true;

    if (v_email) {
        el_e.next().html('<span class="text-error for-email">Поле e-mail обязательно к заполнению</span>');
        el_e.addClass('uk-form-danger');
    } else if (!reg.test(el_e.val())) {
        v_email = true;
        el_e.next().html('<span class="text-error for-email">Вы указали недопустимый e-mail</span>');
        el_e.addClass('uk-form-danger');
    } else {
        el_e.removeClass('uk-form-danger');
    }


    // Проверка телефона

    /*    var reg2     = /^((8|\+7)[\- ]?)?(\(?\d{3,4}\)?[\- ]?)?[\d\- ]{5,10}$/;

        var el_p    = inpPhone;
        var v_phone = el_p.val()?false:true;

        if ( v_phone ) {
            el_p.next().html('<span class="text-error for-phone">Поле телефон обязательно к заполнению</span>');
            el_p.addClass('uk-form-danger');
        } else if ( !reg2.test( el_p.val() ) ) {
            v_phone = true;
            el_p.next().html('<span class="text-error for-phone">Вы указали недопустимый телефон</span>');
            el_p.addClass('uk-form-danger');
        }else{
            el_p.removeClass('uk-form-danger');
        }*/


    // Проверка сообщения

    /*    var el_m = inpMess;
        if ( el_m.val().length < 1 ) {
            var v_mess = true;
            el_m.next().html('<span class="text-error for-login">Заполните сообщение</span>');
            el_m.addClass('uk-form-danger');
        }else{
            el_m.removeClass('uk-form-danger');
        }*/


    return (v_login || v_email || v_phone || v_mess);
}

function sendZayavka(elem) {
    event.preventDefault();
    //console.log(form);
    var li = $(elem);
    var form_data = new FormData(li[0]);
    $.ajax({
        method: 'POST',
        url: '//' + SmGuideWidgetDomain + '/mess',
        dataType: 'html',
        data: form_data,
        cache: false,
        contentType: false,
        processData: false,
        success: function (data) {
            console.log(555);

        },
        error: function (error) {
            console.log(error);
        }
    });

    //alert('готово');
}

//////////////////////////////////////////////////
function getPasteEvent() {
    var el = document.createElement('input'),
        name = 'onpaste';
    el.setAttribute(name, '');
    return (typeof el[name] === 'function') ? 'paste' : 'input';
}

var pasteEventName = getPasteEvent() + ".mask",
    ua = navigator.userAgent,
    iPhone = /iphone/i.test(ua),
    android = /android/i.test(ua),
    caretTimeoutId;

$.mask = {
    //Predefined character definitions
    definitions: {
        '9': "[0-9]",
        'a': "[A-Za-z]",
        '*': "[A-Za-z0-9]"
    },
    dataName: "rawMaskFn",
    placeholder: '_',
};

$.fn.extend({
    //Helper Function for Caret positioning
    caret: function (begin, end) {
        var range;

        if (this.length === 0 || this.is(":hidden")) {
            return;
        }

        if (typeof begin == 'number') {
            end = (typeof end === 'number') ? end : begin;
            return this.each(function () {
                if (this.setSelectionRange) {
                    this.setSelectionRange(begin, end);
                } else if (this.createTextRange) {
                    range = this.createTextRange();
                    range.collapse(true);
                    range.moveEnd('character', end);
                    range.moveStart('character', begin);
                    range.select();
                }
            });
        } else {
            if (this[0].setSelectionRange) {
                begin = this[0].selectionStart;
                end = this[0].selectionEnd;
            } else if (document.selection && document.selection.createRange) {
                range = document.selection.createRange();
                begin = 0 - range.duplicate().moveStart('character', -100000);
                end = begin + range.text.length;
            }
            return {begin: begin, end: end};
        }
    },
    unmask: function () {
        return this.trigger("unmask");
    },
    mask: function (mask, settings) {
        var input,
            defs,
            tests,
            partialPosition,
            firstNonMaskPos,
            len;

        if (!mask && this.length > 0) {
            input = $(this[0]);
            return input.data($.mask.dataName)();
        }
        settings = $.extend({
            placeholder: $.mask.placeholder, // Load default placeholder
            completed: null
        }, settings);


        defs = $.mask.definitions;
        tests = [];
        partialPosition = len = mask.length;
        firstNonMaskPos = null;

        $.each(mask.split(""), function (i, c) {
            if (c == '?') {
                len--;
                partialPosition = i;
            } else if (defs[c]) {
                tests.push(new RegExp(defs[c]));
                if (firstNonMaskPos === null) {
                    firstNonMaskPos = tests.length - 1;
                }
            } else {
                tests.push(null);
            }
        });

        return this.trigger("unmask").each(function () {
            var input = $(this),
                buffer = $.map(
                    mask.split(""),
                    function (c, i) {
                        if (c != '?') {
                            return defs[c] ? settings.placeholder : c;
                        }
                    }),
                focusText = input.val();

            function seekNext(pos) {
                while (++pos < len && !tests[pos]) ;
                return pos;
            }

            function seekPrev(pos) {
                while (--pos >= 0 && !tests[pos]) ;
                return pos;
            }

            function shiftL(begin, end) {
                var i,
                    j;

                if (begin < 0) {
                    return;
                }

                for (i = begin, j = seekNext(end); i < len; i++) {
                    if (tests[i]) {
                        if (j < len && tests[i].test(buffer[j])) {
                            buffer[i] = buffer[j];
                            buffer[j] = settings.placeholder;
                        } else {
                            break;
                        }

                        j = seekNext(j);
                    }
                }
                writeBuffer();
                input.caret(Math.max(firstNonMaskPos, begin));
            }

            function shiftR(pos) {
                var i,
                    c,
                    j,
                    t;

                for (i = pos, c = settings.placeholder; i < len; i++) {
                    if (tests[i]) {
                        j = seekNext(i);
                        t = buffer[i];
                        buffer[i] = c;
                        if (j < len && tests[j].test(t)) {
                            c = t;
                        } else {
                            break;
                        }
                    }
                }
            }

            function keydownEvent(e) {
                var k = e.which,
                    pos,
                    begin,
                    end;

                //backspace, delete, and escape get special treatment
                if (k === 8 || k === 46 || (iPhone && k === 127)) {
                    pos = input.caret();
                    begin = pos.begin;
                    end = pos.end;

                    if (end - begin === 0) {
                        begin = k !== 46 ? seekPrev(begin) : (end = seekNext(begin - 1));
                        end = k === 46 ? seekNext(end) : end;
                    }
                    clearBuffer(begin, end);
                    shiftL(begin, end - 1);

                    e.preventDefault();
                } else if (k == 27) {//escape
                    input.val(focusText);
                    input.caret(0, checkVal());
                    e.preventDefault();
                }
            }

            function keypressEvent(e) {
                var k = e.which,
                    pos = input.caret(),
                    p,
                    c,
                    next;

                if (e.ctrlKey || e.altKey || e.metaKey || k < 32) {//Ignore
                    return;
                } else if (k) {
                    if (pos.end - pos.begin !== 0) {
                        clearBuffer(pos.begin, pos.end);
                        shiftL(pos.begin, pos.end - 1);
                    }

                    p = seekNext(pos.begin - 1);
                    if (p < len) {
                        c = String.fromCharCode(k);
                        if (tests[p].test(c)) {
                            shiftR(p);

                            buffer[p] = c;
                            writeBuffer();
                            next = seekNext(p);

                            if (android) {
                                setTimeout($.proxy($.fn.caret, input, next), 0);
                            } else {
                                input.caret(next);
                            }

                            if (settings.completed && next >= len) {
                                settings.completed.call(input);
                            }
                        }
                    }
                    e.preventDefault();
                }
            }

            function clearBuffer(start, end) {
                var i;
                for (i = start; i < end && i < len; i++) {
                    if (tests[i]) {
                        buffer[i] = settings.placeholder;
                    }
                }
            }

            function writeBuffer() {
                input.val(buffer.join(''));
            }

            function checkVal(allow) {
                //try to place characters where they belong
                var test = input.val(),
                    lastMatch = -1,
                    i,
                    c;

                for (i = 0, pos = 0; i < len; i++) {
                    if (tests[i]) {
                        buffer[i] = settings.placeholder;
                        while (pos++ < test.length) {
                            c = test.charAt(pos - 1);
                            if (tests[i].test(c)) {
                                buffer[i] = c;
                                lastMatch = i;
                                break;
                            }
                        }
                        if (pos > test.length) {
                            break;
                        }
                    } else if (buffer[i] === test.charAt(pos) && i !== partialPosition) {
                        pos++;
                        lastMatch = i;
                    }
                }
                if (allow) {
                    writeBuffer();
                } else if (lastMatch + 1 < partialPosition) {
                    input.val("");
                    clearBuffer(0, len);
                } else {
                    writeBuffer();
                    input.val(input.val().substring(0, lastMatch + 1));
                }
                return (partialPosition ? i : firstNonMaskPos);
            }

            input.data($.mask.dataName, function () {
                return $.map(buffer, function (c, i) {
                    return tests[i] && c != settings.placeholder ? c : null;
                }).join('');
            });

            if (!input.attr("readonly"))
                input
                    .one("unmask", function () {
                        input
                            .unbind(".mask")
                            .removeData($.mask.dataName);
                    })
                    .bind("focus.mask", function () {
                        clearTimeout(caretTimeoutId);
                        var pos,
                            moveCaret;

                        focusText = input.val();
                        pos = checkVal();

                        caretTimeoutId = setTimeout(function () {
                            writeBuffer();
                            if (pos == mask.length) {
                                input.caret(0, pos);
                            } else {
                                input.caret(pos);
                            }
                        }, 10);
                    })
                    .bind("blur.mask", function () {
                        checkVal();
                        if (input.val() != focusText)
                            input.change();
                    })
                    .bind("keydown.mask", keydownEvent)
                    .bind("keypress.mask", keypressEvent)
                    .bind(pasteEventName, function () {
                        setTimeout(function () {
                            var pos = checkVal(true);
                            input.caret(pos);
                            if (settings.completed && pos == input.val().length)
                                settings.completed.call(input);
                        }, 0);
                    });
            checkVal(); //Perform initial check for existing values
        });
    }
});


/*$(document).on('click', '.house-item input:checkbox', function () {

    if ($(this).is(':checked')) {
        $(this).parents('.house-item').find('input:checkbox').not(this).prop('checked', false);

    }

});

$(document).on('click', '.field-chatbots-is_default input:checkbox', function () {

        $('.onoff').toggleClass('uk-hidden');
    if ($(this).is(':checked')) {
        $('.field-chatbots-run').find('input:checkbox').prop('checked', true).attr('disabled',true);
    }else{

        $('.field-chatbots-run').find('input:checkbox').attr('disabled',false);
    }

});

$(document).on('click', 'input.is_first:checkbox', function () {
    if ($(this).is(':checked')) {
        $('input.is_first:checkbox').not(this).prop('checked', false);

    }

});*/








