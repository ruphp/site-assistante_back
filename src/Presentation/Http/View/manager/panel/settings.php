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
    <p>Скопируйте код и разместите его на сайте.</p>
    <div>
        <pre class="uk-resize еее"><code><?php echo $code; ?></code></pre>
    </div>
    <p class="uk-text-muted">
        Подробная инструкция по установке, передаче пользователя, Android-приложению и Telegram-боту вынесена в раздел
        <?= Html::a('Инструкции', ['/manager/instructions']) ?>.
    </p>

</div>
