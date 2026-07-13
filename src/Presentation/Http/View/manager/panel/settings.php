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

    <p>
        Public key проекта:
        <code><?= Html::encode((string)$activeProject->publicKey) ?></code>
    </p>
    <?php
    app\Presentation\Yii\Asset\AppAsset::register($this);
    $form = ActiveForm::begin(['options' => ['id' => 'testForm', 'class' => 'uk-form-stacked']]);

    echo $form->field($params, 'domain',
        ['options' => ['id' => 'testForm', 'class' => 'uk-margin']])
        ->label('URL сайта в формате <code>https://domain.ru</code>. Один проект - один сайт.')
        ->input('string', ['class' => 'uk-input uk-form-width-large']);

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


    <div class="uk-card uk-card-default uk-card-body uk-margin-top">
        <h3 class="uk-margin-remove-top">Код подключения</h3>
        <p class="uk-text-muted uk-margin-small-bottom">
            Скопируйте код ниже и разместите его на нужных страницах сайта перед закрывающим тегом
            <code>&lt;/body&gt;</code> или <code>&lt;/head&gt;</code>.
        </p>
        <div>
            <pre class="uk-resize еее"><code><?php echo $code; ?></code></pre>
        </div>
    </div>

</div>
