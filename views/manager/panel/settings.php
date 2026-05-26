<?php
/**
 * SmirnoVAV
 * Date: 06.03.2019
 * Time: 1:03
 */

//use kartik\color\ColorInput;
use ruwmapps\yii2_uikit3\ActiveForm;
use yii\helpers\Html;

/**
 * @var $cats
 * @var $points
 * @var $tags
 * @var $pages
 * @var $posts
 * @var $code
 */

$this->title = 'Настройки подключения ';
?>

<div class="uk-container uk-position-relative">


    <?php
    app\assets\AppAsset::register($this);
    $form = ActiveForm::begin(['options' => ['id' => 'testForm', 'class' => 'uk-form-stacked']]);

    echo $form->field($params, 'domain',
        ['options' => ['id' => 'testForm', 'class' => 'uk-margin']])
        ->label('URL сайта/сайтов в формате<code>https://domain.ru,https://domain2.ru,https://domain3.ru</code>')->input('string', ['class' => 'uk-input uk-form-width-large']);

    //echo $form->field($params, 'run')->hiddenInput(['value' => 0])->label('');
    echo $form->field($params, 'public_key')->hiddenInput(['value' => Yii::$app->user->identity->getPublicKey()])->label('');

    echo $form->field($params, 'timeout', ['options' => ['id' => 'mail_monitoring', 'class' => 'uk-margin']])->input('string', ['class' => 'uk-input uk-form-width-large']);

    echo $params['is_uuid'] ? $form->field($params, 'is_uuid')->checkbox(['checked ' => '']) : $form->field($params, 'is_uuid')->checkbox();

    echo $params['tab_tp_contacts'] ? $form->field($params, 'tab_tp_contacts')->checkbox(['checked ' => '', 'class' => ' check_tp_contacts']) : $form->field($params, 'tab_tp_contacts')->checkbox(['class' => ' check_tp_contacts']);
    if ($params['tab_tp_contacts']) {
        echo $form->field($params, 'tp_contacts')->textarea(['rows' => '6', 'id' => 'testFormContacts', 'class' => 'uk-margin tp_contacts'])->label(null, ['class' => 'tp_contacts']);
    } else {
        echo $form->field($params, 'tp_contacts')->textarea(['rows' => '6', 'id' => 'testFormContacts', 'class' => 'tp_contacts uk-margin uk-hidden'])->label(null, ['class' => 'tp_contacts uk-hidden']);
    }
    if (Yii::$app->user->identity->isPremissionByName('chatbots')) {
        echo $form->field($params, 'default_answer')->textarea(['rows' => '6', 'id' => 'default_answer', 'class' => 'uk-margin']);
    }
    if (Yii::$app->user->identity->isPremissionByName('bigdata')) {
        echo $form->field($params, 'chatbot_bigdata_system_id')->input('integer');
        echo $params['chatbot_bigdata_is_active'] ? $form->field($params, 'chatbot_bigdata_is_active')->checkbox(['checked ' => '']) : $form->field($params, 'chatbot_bigdata_is_active')->checkbox();

    }


    // кнопка
    echo Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']);
    ActiveForm::end();
    ?>
    <h3>Инструкция по подключению</h3>
    <p>Скопируйте этот код</p>
    <div>
        <pre class="uk-resize еее"><code><?php echo $code; ?></code></pre>
    </div>
    и разместите его на сайте, на нужных страницах перед закрывающим тегом
    <code>&lsaquo;/body&rsaquo;</code> или <code>&lsaquo;/head&rsaquo;</code> .


    <p>Как Назначить пользователя:</p>


    <ul>
        <li>для указания идентификатора пользователя задайте его идентификатор в параметре id (тип параметра integer - 1234
            или BigInt - 6657365633458205532n

            <pre class="uk-resize еее"><code>
    ...
    <code>id: 1234</code>,
    role: [4],
    name: 'Some Name',
    email: 'somemail@gmail.com'
    ...
</code></pre>
        </li>
    </ul>

    <p>
        Как назначить роли пользователя: </p>

    <ul>
        <li>для одной роли задайте идентификатор роли в массиве (тип параметра array[integer] ):

            <pre class="uk-resize еее"><code>
    ...
    id: 1234,
    <code>role: [4]</code>,
    name: 'Some Name',
    email: 'somemail@gmail.com'
    ...
</code></pre>
        </li>
        <li>для нескольких ролей устанавливают идентификаторы ролей в массиве (тип параметра array[integer] ):

            <pre class="uk-resize еее"><code>
    ...
    id: 1234,
    <code>role: [4, 5, 6]</code>,
    name: 'Some Name',
    email: 'somemail@gmail.com'
    ...
</code></pre>
        </li>
    </ul>
    <p>
        Обновление виджета через команду js:</p>

    <ul>
        <li>Если на странице есть элементы, которые добавляются динамически.</br>
            То в коде js, вы можете вызвать функцию виджета для его обновления и он увидит новые элементы
            <pre class="uk-resize еее"><code><code>window.Smartius.api.update();</code></code></pre>
        </li>
    </ul>


</div>
