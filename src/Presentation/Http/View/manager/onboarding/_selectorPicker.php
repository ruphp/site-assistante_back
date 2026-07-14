<?php

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * @var string $inputId
 * @var string $defaultUrl
 * @var string $target
 */

$pickerId = 'selector-picker-' . preg_replace('/[^a-zA-Z0-9_-]+/', '-', $inputId);
$sessionUrl = Url::to(['/manager/onboarding/selector-session', 'projectId' => Yii::$app->request->get('projectId')]);
$statusUrl = Url::to(['/manager/onboarding/selector-status', 'projectId' => Yii::$app->request->get('projectId')]);
$statusTokenGlue = strpos($statusUrl, '?') === false ? '?' : '&';
?>

<div class="uk-margin-small-top sw-selector-picker" id="<?= Html::encode($pickerId) ?>">
    <div class="uk-grid-small" uk-grid>
        <div class="uk-width-expand">
            <?= Html::input('text', null, $defaultUrl, [
                'class' => 'uk-input uk-form-small js-selector-url',
                'placeholder' => 'URL страницы, где выбираем элемент',
            ]) ?>
        </div>
        <div class="uk-width-auto">
            <button type="button" class="uk-button uk-button-default uk-button-small js-selector-start">Выбрать на сайте</button>
        </div>
        <div class="uk-width-auto">
            <button type="button" class="uk-button uk-button-default uk-button-small js-selector-fetch" disabled>Получить выбранный</button>
        </div>
    </div>
    <div class="uk-text-meta uk-margin-small-top js-selector-message">
        Откройте страницу, зажмите значок пазла виджета на 5 секунд или используйте ссылку выбора. Затем кликните по нужному элементу.
    </div>
</div>

<?php
$js = <<<JS
(function () {
    var root = document.getElementById('$pickerId');
    if (!root) return;
    var targetInput = document.getElementById('$inputId');
    var urlInput = root.querySelector('.js-selector-url');
    var startButton = root.querySelector('.js-selector-start');
    var fetchButton = root.querySelector('.js-selector-fetch');
    var message = root.querySelector('.js-selector-message');
    var token = '';
    var timer = null;

    function csrf() {
        var param = document.querySelector('meta[name="csrf-param"]');
        var tokenMeta = document.querySelector('meta[name="csrf-token"]');
        return param && tokenMeta ? encodeURIComponent(param.content) + '=' + encodeURIComponent(tokenMeta.content) + '&' : '';
    }

    function setMessage(text) {
        message.textContent = text;
    }

    function checkStatus() {
        if (!token) return;
        fetch('$statusUrl{$statusTokenGlue}token=' + encodeURIComponent(token), {credentials: 'same-origin'})
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.ok) {
                    setMessage(data.message || 'Не удалось получить выбранный элемент.');
                    return;
                }
                if (!data.selected) {
                    setMessage('Пока элемент не выбран. Перейдите на открытую страницу и кликните по нужному элементу.');
                    return;
                }
                targetInput.value = data.selector;
                targetInput.dispatchEvent(new Event('input', {bubbles: true}));
                setMessage('Выбран элемент: ' + (data.element || data.selector));
                if (timer) window.clearInterval(timer);
            })
            .catch(function () {
                setMessage('Не удалось проверить выбор элемента.');
            });
    }

    startButton.addEventListener('click', function () {
        var body = csrf() + 'target=' + encodeURIComponent('$target') + '&url=' + encodeURIComponent(urlInput.value || '');
        fetch('$sessionUrl', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'},
            body: body
        })
            .then(function (response) { return response.json(); })
            .then(function (data) {
                if (!data.ok) {
                    setMessage(data.message || 'Не удалось создать режим выбора.');
                    return;
                }
                token = data.token;
                fetchButton.disabled = false;
                setMessage('Режим выбора открыт. Кликните по нужному элементу на сайте.');
                window.open(data.url, '_blank');
                if (timer) window.clearInterval(timer);
                timer = window.setInterval(checkStatus, 2500);
            })
            .catch(function () {
                setMessage('Не удалось открыть режим выбора.');
            });
    });

    fetchButton.addEventListener('click', checkStatus);
})();
JS;
$this->registerJs($js);
?>
