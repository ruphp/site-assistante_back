<?php

use app\Application\Panel\Dto\ClientProjectView;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleUrlRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use app\Infrastructure\YiiActiveRecord\Roles;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @var InstructionArticleRecord $article
 * @var InstructionCategoryRecord[] $categories
 * @var Roles[] $roles
 * @var int[] $selectedRoleIds
 * @var InstructionArticleUrlRecord[] $articleUrls
 * @var bool $urlBindingsEnabled
 * @var ClientProjectView[] $projects
 * @var ClientProjectView $activeProject
 */

$this->title = $article->isNewRecord ? 'Создать инструкцию' : 'Редактировать инструкцию';
$categoryOptions = ['' => 'Без раздела'] + ArrayHelper::map($categories, 'id', 'name');
$urlText = implode("\n", array_map(static function (InstructionArticleUrlRecord $url): string {
    return $url->url . '|' . ((bool)$url->include_children ? 'children' : '') . '|' . ((bool)$url->include_query ? 'query' : '');
}, $articleUrls));
?>

<div class="uk-container uk-margin">
    <?= $this->render('@app/src/Presentation/Http/View/manager/panel/_projectTabs', compact('projects', 'activeProject')) ?>
    <?= $this->render('_nav', compact('activeProject')) ?>

    <h3><?= Html::encode($this->title) ?></h3>
    <?= Html::beginForm('', 'post', ['class' => 'uk-form-stacked sw-instruction-form']) ?>
        <div class="uk-margin">
            <label class="uk-form-label">Заголовок</label>
            <input class="uk-input" name="InstructionArticle[title]" value="<?= Html::encode($article->title) ?>" placeholder="Например: Как оформить заказ">
        </div>
        <div class="uk-grid-small" uk-grid>
            <div class="uk-width-1-2@m">
                <label class="uk-form-label">Раздел</label>
                <?= Html::dropDownList('InstructionArticle[category_id]', $article->category_id, $categoryOptions, ['class' => 'uk-select']) ?>
            </div>
            <div class="uk-width-1-4@m">
                <label class="uk-form-label">Порядок</label>
                <input class="uk-input" type="number" name="InstructionArticle[sort_order]" value="<?= Html::encode((string)$article->sort_order) ?>">
            </div>
            <div class="uk-width-1-4@m uk-flex uk-flex-bottom">
                <label><input class="uk-checkbox" type="checkbox" name="InstructionArticle[is_active]" value="1" <?= $article->is_active ? 'checked' : '' ?>> Включена</label>
            </div>
        </div>
        <div class="uk-margin">
            <label class="uk-form-label">Содержание инструкции</label>
            <div class="sw-instruction-editor-toolbar" role="toolbar" aria-label="Редактор инструкции">
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="formatBlock" data-value="h3">H3</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="bold"><b>B</b></button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="italic"><i>I</i></button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="insertUnorderedList">Список</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="insertOrderedList">1.2.</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-action="link">Ссылка</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-action="image">Изображение</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-action="video">Видео</button>
                <button class="uk-button uk-button-default uk-button-small sw-editor-button" type="button" data-command="removeFormat">Очистить</button>
            </div>
            <div class="sw-instruction-editor-view" contenteditable="true" data-editor-view><?= $article->html ?></div>
            <textarea class="sw-instruction-editor-source" name="InstructionArticle[html]" data-editor-source hidden style="display:none"><?= Html::encode($article->html) ?></textarea>
            <div class="uk-text-meta sw-instruction-editor-help">
                Изображения можно вставлять по прямой ссылке: с вашего сайта, Cloudinary, ImageKit, Uploadcare, ImgBB или другого сервиса.
                Видео удобнее вставлять ссылкой с YouTube, Rutube или VK Video.
            </div>
        </div>
        <?php if ($roles !== []): ?>
            <div class="uk-margin">
                <label class="uk-form-label">Роли пользователя сайта</label>
                <div class="sw-instruction-role-list">
                    <?php foreach ($roles as $role): ?>
                        <label>
                            <input class="uk-checkbox" type="checkbox" name="InstructionArticle[role_ids][]" value="<?= Html::encode((string)$role->id) ?>" <?= in_array((int)$role->id, array_map('intval', $selectedRoleIds), true) ? 'checked' : '' ?>>
                            <?= Html::encode($role->name) ?> <span class="uk-text-meta">ID <?= Html::encode((string)$role->id_role_in_system) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
                <div class="uk-text-meta">Если не выбрать ни одной роли, инструкция видна всем посетителям.</div>
            </div>
        <?php endif; ?>
        <div class="uk-margin">
            <label class="uk-form-label">URL-привязки</label>
            <?php if ($urlBindingsEnabled): ?>
                <textarea class="uk-textarea" name="InstructionArticle[urls]" rows="5" placeholder="/catalog|children&#10;/checkout||query"><?= Html::encode($urlText) ?></textarea>
                <div class="uk-text-meta">Формат строки: URL|children|query. Если список пустой, инструкция видна на всех страницах.</div>
            <?php else: ?>
                <div class="uk-alert-primary" uk-alert>URL-привязки доступны в Pro-тарифе. Сейчас инструкция будет общей или ограниченной только ролями.</div>
            <?php endif; ?>
        </div>
        <button class="uk-button uk-button-primary" type="submit">Сохранить</button>
        <?= Html::a('Отмена', ['/manager/instructions', 'projectId' => $activeProject->id], ['class' => 'uk-button uk-button-default']) ?>
    <?= Html::endForm() ?>
</div>

<?php
$this->registerJs(<<<'JS'
(function () {
    const editor = document.querySelector('[data-editor-view]');
    const source = document.querySelector('[data-editor-source]');
    if (!editor || !source) {
        return;
    }

    const syncSource = () => {
        source.value = editor.innerHTML.trim();
    };

    const insertHtml = html => {
        editor.focus();
        document.execCommand('insertHTML', false, html);
        syncSource();
    };

    const escapeAttr = value => String(value)
        .replace(/&/g, '&amp;')
        .replace(/"/g, '&quot;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;');

    const buildVideoHtml = url => {
        const safeUrl = escapeAttr(url);
        const youtube = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([A-Za-z0-9_-]+)/);
        const rutube = url.match(/rutube\.ru\/video\/([A-Za-z0-9]+)/);
        if (youtube) {
            return '<div class="sw-instruction-media"><iframe src="https://www.youtube.com/embed/' + escapeAttr(youtube[1]) + '" allowfullscreen loading="lazy"></iframe></div>';
        }
        if (rutube) {
            return '<div class="sw-instruction-media"><iframe src="https://rutube.ru/play/embed/' + escapeAttr(rutube[1]) + '" allowfullscreen loading="lazy"></iframe></div>';
        }

        return '<p><a href="' + safeUrl + '" target="_blank" rel="noopener noreferrer">' + safeUrl + '</a></p>';
    };

    document.querySelectorAll('[data-command]').forEach(button => {
        button.addEventListener('click', () => {
            editor.focus();
            document.execCommand(button.dataset.command, false, button.dataset.value || null);
            syncSource();
        });
    });

    document.querySelectorAll('[data-action]').forEach(button => {
        button.addEventListener('click', () => {
            editor.focus();
            if (button.dataset.action === 'link') {
                const url = window.prompt('URL ссылки');
                if (url) {
                    document.execCommand('createLink', false, url);
                }
            }
            if (button.dataset.action === 'image') {
                const url = window.prompt('Прямая ссылка на изображение');
                if (url) {
                    insertHtml('<img src="' + escapeAttr(url) + '" alt="">');
                }
            }
            if (button.dataset.action === 'video') {
                const url = window.prompt('Ссылка на видео');
                if (url) {
                    insertHtml(buildVideoHtml(url));
                }
            }
            syncSource();
        });
    });

    editor.addEventListener('input', syncSource);
    editor.closest('form')?.addEventListener('submit', syncSource);
})();
JS);
?>
