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
 * @var \app\Domain\Client\ClientModuleAccess $moduleAccess
 * @var \app\Application\Panel\Dto\ClientProjectView[] $projects
 * @var \app\Application\Panel\Dto\ClientProjectView $activeProject
 */

$this->title = 'Настройки подключения ';
?>

<div class="uk-container uk-position-relative">
    <?= $this->render('_projectTabs', compact('projects', 'activeProject')) ?>


    <?php
    app\Presentation\Yii\Asset\AppAsset::register($this);
    $form = ActiveForm::begin(['options' => ['id' => 'testForm', 'class' => 'uk-form-stacked']]);

    echo $form->field($params, 'domain',
        ['options' => ['id' => 'testForm', 'class' => 'uk-margin']])
        ->label('URL сайта/сайтов в формате<code>https://domain.ru,https://domain2.ru,https://domain3.ru</code>')->input('string', ['class' => 'uk-input uk-form-width-large']);

    //echo $form->field($params, 'run')->hiddenInput(['value' => 0])->label('');

    echo $form->field($params, 'timeout', ['options' => ['id' => 'mail_monitoring', 'class' => 'uk-margin']])->input('string', ['class' => 'uk-input uk-form-width-large']);

    echo $params['is_uuid'] ? $form->field($params, 'is_uuid')->checkbox(['checked ' => '']) : $form->field($params, 'is_uuid')->checkbox();

    if ($moduleAccess->allows('chatbots')) {
        echo $form->field($params, 'default_answer')->textarea(['rows' => '6', 'id' => 'default_answer', 'class' => 'uk-margin']);
    }
    if ($moduleAccess->allows('bigdata')) {
        echo $form->field($params, 'chatbot_bigdata_system_id')->input('integer');
        echo $params['chatbot_bigdata_is_active'] ? $form->field($params, 'chatbot_bigdata_is_active')->checkbox(['checked ' => '']) : $form->field($params, 'chatbot_bigdata_is_active')->checkbox();

    }


    // кнопка
    echo Html::submitButton('Сохранить', ['class' => 'uk-button uk-button-primary']);
    ActiveForm::end();
    ?>
    <h3>Код подключения</h3>
    <p>
        Public key проекта:
        <code><?= Html::encode((string)$activeProject->publicKey) ?></code>
    </p>
    <h3>Инструкция по подключению</h3>
    <p>Скопируйте этот код</p>
    <div>
        <pre class="uk-resize еее"><code><?php echo $code; ?></code></pre>
    </div>
    <p>И разместите его на сайте, на нужных страницах перед закрывающим тегом
        <code>&lsaquo;/body&rsaquo;</code> или <code>&lsaquo;/head&rsaquo;</code>.
    </p>

    <p><strong>Как назначить пользователя:</strong></p>
    <ul>
        <li>Для указания идентификатора пользователя задайте его идентификатор в параметре <code>id</code>
            (тип параметра integer - 1234 или BigInt - 6657365633458205532n).
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

    <p><strong>Как назначить роли пользователя:</strong></p>
    <ul>
        <li>Для одной роли задайте идентификатор роли в массиве (тип параметра array[integer]):
            <pre class="uk-resize еее"><code>
    ...
    id: 1234,
    <code>role: [4]</code>,
    name: 'Some Name',
    email: 'somemail@gmail.com'
    ...
</code></pre>
        </li>
        <li>Для нескольких ролей устанавливают идентификаторы ролей в массиве (тип параметра array[integer]):
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

    <p><strong>Обновление виджета через команду js:</strong></p>
    <ul>
        <li>Если на странице есть элементы, которые добавляются динамически,
            то в коде js вы можете вызвать функцию виджета для его обновления, и он увидит новые элементы:
            <pre class="uk-resize еее"><code>window.SiteWidget.api.update();</code></pre>
        </li>
    </ul>

</div>
