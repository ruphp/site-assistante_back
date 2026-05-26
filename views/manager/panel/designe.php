<?php
/**
 * SmirnoVAV
 * Date: 06.03.2019
 * Time: 1:03
 */


use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;
use app\widgets\SgCodemirror as Codemirror;

/**
 * @var $cats
 * @var $points
 * @var $tags
 * @var $pages
 * @var $posts
 * @var $params
 */

$this->title = 'Оформление ';

?>

    <div class="uk-container uk-position-relative">


<?php
app\assets\AppAsset::register($this);
$form = ActiveForm::begin(['options' => ['id' => 'testForm', 'class' => 'uk-form-stacked']]);
echo $form->field($params, 'public_key')->hiddenInput(['value' => Yii::$app->user->identity->getPublicKey()])->label('');

echo $form->field($params, 'leftbutton')->radioList(

    [0 => 'По правому краю', 1 => 'По левому краю']
);
?>

<?php
echo $form->field($params, 'design')->radioList(
    ['dark' => 'Тёмная', 'light' => 'Светлая', 'green' => 'Зелёная', 'blue' => 'Голубая',],

    [
        'id' => 'ts-radio',
        'class' => 'uk-margin uk-grid-small uk-child-width-auto uk-grid',
        'data-toggle' => 'buttons',
        'unselect' => null,
        'separator' => '&nbsp;&nbsp;&nbsp;</br>',
        'item' => function ($index, $label, $name, $checked, $value) {
            return '<label class="btn btn-primary' . ($checked ? ' active' : '') . '">' .
                Html::radio($name, $checked, ['value' => $value, 'class' => 'project-status-btn']) . $label . '</label>';
        },
    ]);

?>

    <div class="uk-card uk-margin-bottom">
        <div id="fon_logo" class="color-<?php echo $params->design ?>  fon_logo">
            <svg width="38" height="38" viewBox="0 0 38 24" fill="#3d8af5" xmlns="http://www.w3.org/2000/svg">
                <path d="M15.7171 15.4784L14.1244 14.6367L22.5398 0L24.1022 0.871922L15.7171 15.4784Z"></path>
                <path d="M11.5063 3.48226V1.66556L0 7.23656L11.5063 13.1106V11.3544L3.67137 7.29702L11.5063 3.48226Z"></path>
                <path d="M26.4945 3.48223V1.66553L38 7.23653L28.4112 12.1414V19.4473C25.4823 23.0427 19.2231 24.0681 14.3907 22.5266L15.3548 20.847V20.8455C19.1386 21.9701 23.723 21.2687 26.5069 18.7358L26.5076 13.1151L24.0725 14.3607V12.6045L34.3286 7.29698L26.4945 3.48223Z"></path>
            </svg>
        </div>
    </div>

<?php
echo $form->field($designe, 'LogoSvg')->widget(Codemirror::class, [
    'preset' => Codemirror::PRESET_JS, // Example: for PHP code highlighting
    //'lineWrapping' => true,
    'toolbar' => [
        'actions' => ['buttons' => ['undo', 'redo', 'selectall']],
        'view' => ['buttons' => ['fullscreen'], 'options' => ['class' => 'pull-right ml-auto']],
    ]

])->label('<p>Код svg иконки</p>');
;

echo $form->field($designe, 'CustomCss')->widget(Codemirror::class, [
    'toolbar' => [
        'actions' => ['buttons' => ['undo', 'redo', 'selectall']],
        'edit' => [],
        'format' => [],
        'comment' => [],
        'view' => ['buttons' => ['fullscreen'], 'options' => ['class' => 'pull-right ml-auto']],]
])->label('<p>Дополнительный css <a href="#modal-question" uk-toggle uk-icon="question"></a> </p>');


echo Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']);
ActiveForm::end();
?>
    <div id="modal-question" class="uk-modal-container" uk-modal>
        <div class="uk-modal-dialog u uk-modal-body" uk-overflow-auto>
            <button class="uk-modal-close-default" type="button" uk-close></button>
            <p class="uk-h4">Инструкция: как определить класс элемента на странице и изменить внешний вид, через
                поле «Дополнительный css»</p>
            <ul  class="uk-list uk-list-divider">

                <li>1. Откройте страницу внешней ИС.</li>

                <li>2. Откройте панель разработчика в браузере, можно несколькими способами:
                    <ul>
                        <li>- с помощью горячей клавиши <code>F12</code></li>
                        <li>- сочетанием клавиш <code>Ctrl + Shift + I</code> (Windows/Linux) / <code>Cmd + Option + I</code> (macOS)</li>
                        <li>- через контекстное меню, нажав
                            правой кнопкой мыши на странице и выбрав «Просмотреть код»
                        </li>
                        <li>- через меню браузера, выбрав
                            «Дополнительные инструменты» > «Инструменты разработчика»
                        </li>
                    </ul>

                </li>

                <li>3. Откроется панель разработчика (Developer Tools) — обычно внизу или справа окна браузера.
                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_1.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_1.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>4. В панели нажмите на значок стрелочки в левом верхнем углу (он называется Select an element in the
                    page to inspect
                    it).
                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_2.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_2.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>5. Наведите курсор на нужный элемент на странице (например, сообщение в чате). Когда элемент
                    подсветится, щёлкните по
                    нему левой кнопкой мыши.
                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_3.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_3.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>6. В панели разработчика перейдите к подсвеченной строке, <br>например:
                    <code>&lsaquo;div class="sc-gFVvzn jGOWmT outbound"&rsaquo;...</code>

                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_4.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_4.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>7. Скопируйте содержимое после <code>class=</code> в кавычках:
                    <code>sc-gFVvzn jGOWmT outbound</code></li>

                <li>8. Преобразуйте это в формат CSS-класса:
                    <br>– замените пробелы на точки
                    <br>– добавьте точку в начале
                    <br>Например: <code>.sc-gFVvzn.jGOWmT.outbound</code>

                <li>9. Вставьте получившийся класс в поле «Дополнительный css» на портале ЦИПП. Добавьте фигурные скобки { },
                    укажите нужные параметры оформления и нажмите кнопку «Сохранить»
                    <br>Пример изменения фона сообщения на красный:
                    <code>.sc-gFVvzn.jGOWmT.outbound {
                        background-color: red !important;
                        }</code>

                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_5.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_5.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

                <li>10. Теперь можно обновить страницу внешней ИС и увидеть изменения.
                    <div uk-lightbox>
                        <a class="uk-inline uk-width-1-3" href="/images/Screenshot_6.jpg" data-caption="Описание 1">
                            <img src="/images/Screenshot_6.jpg" alt="">
                            <div class="uk-position-center">
                                <span uk-icon="search"></span>
                            </div>
                        </a>
                    </div>
                </li>

            </ul>
<hr>
            <p><span class="uk-text-large">*</span> <code>!important</code> - это указание браузеру, что данный стиль должен применяться в любом случае, даже если
                есть
                другие стили, которые его перекрывают.
            </p>

            <p> Посмотреть актуальные на данный момент CSS-свойства (стили) элемента (background-color, border-radius и т.п.) можно в панели
                разработчика на вкладке Styles (Стили), там отображаются все параметры выбранного элемента (цвет,
                отступы, рамки, шрифт и др.).
            <div uk-lightbox>
                <a class="uk-inline uk-width-1-3" href="/images/Screenshot_7.jpg" data-caption="Описание 1">
                    <img src="/images/Screenshot_7.jpg" alt="">
                    <div class="uk-position-center">
                        <span uk-icon="search"></span>
                    </div>
                </a>
            </div>
            </p>

            <p> Более подробную информацию по CSS можно узнать на сайте: https://htmlbook.ru/css</p>
        </div>
    </div>

<?php
$js = <<<JS

$('#fon_logo').removeClass().addClass('color-' + $('input.project-status-btn:checked').val()) ;

$('#fon_logo').html($('#designe-logosvg').val()) ;

$('input.project-status-btn').click(function(){

	$('#fon_logo').removeClass().addClass('color-' + $(this).val()) ;
});



JS;
$this->registerJs($js);
$this->registerJsFile('@web/js/jquery-ui.min.js',
    [
        'depends' => 'yii\web\YiiAsset', // зависимости для скрипта
        'position' => $this::POS_END   // подключать в <head>
    ]);;
$this->registerCssFile('@web/css/fontawesome.css');;


