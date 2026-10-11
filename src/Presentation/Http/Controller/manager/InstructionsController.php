<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientProjectService;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Domain\InstructionPlanLimit;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRoleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleUrlRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionSettingsRecord;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

final class InstructionsController extends ManagerController
{
    public function actionIndex(): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $articles = $this->articles($publicKey);
        $limit = InstructionPlanLimit::forPlan($this->supportPlan($ownerPublicKey));

        return $this->render('index', [
            'articles' => $articles,
            'categories' => $this->categories($publicKey),
            'totalViews' => array_sum(array_map(static fn(InstructionArticleRecord $article): int => (int)$article->views, $articles)),
            'totalLikes' => array_sum(array_map(static fn(InstructionArticleRecord $article): int => (int)$article->likes, $articles)),
            'totalComments' => $this->commentsCount($articles),
            'storageBytes' => array_sum(array_map(static fn(InstructionArticleRecord $article): int => (int)$article->content_bytes, $articles)),
            'storageLimitBytes' => $limit->storageBytes,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionSections(): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $limit = InstructionPlanLimit::forPlan($this->supportPlan($ownerPublicKey));

        if (Yii::$app->request->isPost) {
            $this->saveCategory($publicKey, $limit);

            return $this->redirect($this->projectUrl('/manager/instructions/sections', $projectId));
        }

        return $this->render('sections', [
            'categories' => $this->categories($publicKey),
            'nestedCategoriesEnabled' => $limit->nestedCategoriesEnabled,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionCreate(): Response|string
    {
        return $this->editArticle(null);
    }

    public function actionUpdate(int $id): Response|string
    {
        return $this->editArticle($id);
    }

    public function actionStats(int $id): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $article = InstructionArticleRecord::findOne(['id' => $id, 'public_key' => $publicKey]);
        if (!$article instanceof InstructionArticleRecord) {
            Yii::$app->session->setFlash('error', 'Инструкция не найдена');

            return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
        }

        return $this->render('stats', [
            'article' => $article,
            'comments' => InstructionArticleFeedbackRecord::find()
                ->where(['article_id' => $article->id])
                ->andWhere(['<>', 'comment', ''])
                ->orderBy(['id' => SORT_DESC])
                ->limit(50)
                ->all(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionDeleteCategory(int $id): Response
    {
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        if ($this->isOwnerUser()) {
            $projects = Yii::$container->get(ClientProjectService::class);
            $publicKey = $projects->publicKeyForProject(Yii::$app->user->identity->getPublicKey(), $projectId);
            $hasInstructions = InstructionArticleRecord::find()->where(['category_id' => $id, 'public_key' => $publicKey])->exists();
            if ($hasInstructions) {
                Yii::$app->session->setFlash('error', 'В разделе есть инструкции. Сначала перенесите их в другой раздел.');
            } else {
                InstructionCategoryRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);
                Yii::$app->session->setFlash('success', 'Раздел удален');
            }
        }

        return $this->redirect($this->projectUrl('/manager/instructions/sections', $projectId));
    }

    public function actionDeleteArticle(int $id): Response
    {
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        if ($this->isOwnerUser()) {
            $projects = Yii::$container->get(ClientProjectService::class);
            $publicKey = $projects->publicKeyForProject(Yii::$app->user->identity->getPublicKey(), $projectId);
            InstructionArticleRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);
            Yii::$app->session->setFlash('success', 'Инструкция удалена');
        }

        return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
    }

    private function editArticle(?int $id): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $limit = InstructionPlanLimit::forPlan($this->supportPlan($ownerPublicKey));
        $article = $id === null
            ? new InstructionArticleRecord(['public_key' => $publicKey, 'is_active' => true, 'sort_order' => 100])
            : InstructionArticleRecord::findOne(['id' => $id, 'public_key' => $publicKey]);

        if (!$article instanceof InstructionArticleRecord) {
            Yii::$app->session->setFlash('error', 'Инструкция не найдена');

            return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $this->saveArticle($publicKey, $article, $limit);

            return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
        }

        return $this->render('form', [
            'article' => $article,
            'categories' => $this->categories($publicKey),
            'roles' => $limit->rolesEnabled ? Roles::find()->where(['public_key' => $publicKey])->orderBy(['name' => SORT_ASC])->all() : [],
            'selectedRoleIds' => !$limit->rolesEnabled || $article->isNewRecord ? [] : InstructionArticleRoleRecord::find()->where(['article_id' => $article->id])->select('role_id')->column(),
            'articleUrls' => $article->isNewRecord ? [] : InstructionArticleUrlRecord::find()->where(['article_id' => $article->id])->orderBy(['id' => SORT_ASC])->all(),
            'urlBindingsEnabled' => $limit->urlBindingsEnabled,
            'rolesEnabled' => $limit->rolesEnabled,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    private function saveCategory(int $publicKey, InstructionPlanLimit $limit): void
    {
        $data = Yii::$app->request->post('InstructionCategory');
        if (!is_array($data)) {
            return;
        }

        $category = empty($data['id'])
            ? new InstructionCategoryRecord(['public_key' => $publicKey])
            : InstructionCategoryRecord::findOne(['id' => (int)$data['id'], 'public_key' => $publicKey]);

        if (!$category instanceof InstructionCategoryRecord) {
            return;
        }

        if ($category->isNewRecord && $limit->maxCategories !== null) {
            $categoryCount = (int)InstructionCategoryRecord::find()->where(['public_key' => $publicKey])->count();
            if ($categoryCount >= $limit->maxCategories) {
                Yii::$app->session->setFlash('error', 'На тарифе Free можно создать до 5 разделов инструкций');

                return;
            }
        }

        if (!$limit->nestedCategoriesEnabled && !empty($data['parent_id'])) {
            Yii::$app->session->setFlash('error', 'Вложенные разделы доступны на тарифе Start');

            return;
        }

        $category->name = trim((string)($data['name'] ?? ''));
        $category->parent_id = empty($data['parent_id']) ? null : (int)$data['parent_id'];
        $category->sort_order = (int)($data['sort_order'] ?? 100);
        $category->is_active = (bool)($data['is_active'] ?? false);

        $saved = $category->save();
        Yii::$app->session->setFlash($saved ? 'success' : 'error', $saved ? 'Раздел сохранен' : 'Не удалось сохранить раздел');
    }

    private function saveArticle(int $publicKey, InstructionArticleRecord $article, InstructionPlanLimit $limit): void
    {
        $data = Yii::$app->request->post('InstructionArticle');
        if (!is_array($data)) {
            return;
        }

        if ($article->isNewRecord && $this->creationLocked($publicKey)) {
            Yii::$app->session->setFlash('error', 'Создание инструкций временно заблокировано');

            return;
        }

        $article->public_key = $publicKey;
        $article->title = trim((string)($data['title'] ?? ''));
        $article->html = (string)($data['html'] ?? '');
        $article->category_id = empty($data['category_id']) ? null : (int)$data['category_id'];
        $article->sort_order = (int)($data['sort_order'] ?? 100);
        $article->is_active = (bool)($data['is_active'] ?? false);

        if (!$this->articleCountAllowed($publicKey, $article, $limit)) {
            return;
        }

        if (!$this->storageAllowed($publicKey, $article, $limit)) {
            Yii::$app->session->setFlash('error', 'Лимит места для инструкций исчерпан');

            return;
        }

        $saved = $article->save();
        if ($saved) {
            if ($limit->rolesEnabled) {
                $this->saveArticleRoles($article, $data);
            }
            $this->saveArticleUrls($publicKey, $article, $data, $limit);
        }
        Yii::$app->session->setFlash($saved ? 'success' : 'error', $saved ? 'Инструкция сохранена' : 'Не удалось сохранить инструкцию');
    }

    private function saveArticleRoles(InstructionArticleRecord $article, array $data): void
    {
        InstructionArticleRoleRecord::deleteAll(['article_id' => $article->id]);
        $roleIds = $data['role_ids'] ?? [];
        if (!is_array($roleIds)) {
            return;
        }

        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            if ($roleId <= 0) {
                continue;
            }
            (new InstructionArticleRoleRecord([
                'article_id' => $article->id,
                'role_id' => $roleId,
            ]))->save(false);
        }
    }

    private function saveArticleUrls(int $publicKey, InstructionArticleRecord $article, array $data, InstructionPlanLimit $limit): void
    {
        if (!$limit->urlBindingsEnabled) {
            return;
        }

        InstructionArticleUrlRecord::deleteAll(['article_id' => $article->id]);
        $rawUrls = preg_split('/\r\n|\r|\n/', (string)($data['urls'] ?? '')) ?: [];
        foreach ($rawUrls as $rawUrl) {
            $rawUrl = trim($rawUrl);
            if ($rawUrl === '') {
                continue;
            }

            [$url, $children, $query] = array_pad(array_map('trim', explode('|', $rawUrl)), 3, '');
            (new InstructionArticleUrlRecord([
                'article_id' => $article->id,
                'public_key' => $publicKey,
                'url' => $url,
                'include_children' => in_array($children, ['1', 'yes', 'true', 'children'], true),
                'include_query' => in_array($query, ['1', 'yes', 'true', 'query'], true),
            ]))->save(false);
        }
    }

    private function creationLocked(int $publicKey): bool
    {
        $settings = InstructionSettingsRecord::findOne($publicKey);

        return $settings instanceof InstructionSettingsRecord && (bool)$settings->creation_locked;
    }

    private function articleCountAllowed(int $publicKey, InstructionArticleRecord $article, InstructionPlanLimit $limit): bool
    {
        if ($limit->maxArticlesPerCategory === null) {
            return true;
        }

        if ($article->category_id === null) {
            Yii::$app->session->setFlash('error', 'На тарифе Free инструкция должна находиться в разделе');

            return false;
        }

        $categoryExists = InstructionCategoryRecord::find()
            ->where(['id' => $article->category_id, 'public_key' => $publicKey])
            ->exists();
        if (!$categoryExists) {
            Yii::$app->session->setFlash('error', 'Выбранный раздел не найден');

            return false;
        }

        $query = InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey, 'category_id' => $article->category_id])
            ->andFilterWhere(['<>', 'id', $article->id ?: null]);
        if ((int)$query->count() >= $limit->maxArticlesPerCategory) {
            Yii::$app->session->setFlash('error', 'На тарифе Free в одном разделе можно создать до 10 статей');

            return false;
        }

        return true;
    }

    private function storageAllowed(int $publicKey, InstructionArticleRecord $article, InstructionPlanLimit $limit): bool
    {
        if (!$limit->enabled) {
            return false;
        }

        $query = InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey])
            ->andFilterWhere(['<>', 'id', $article->id ?: null]);
        $usedBytes = (int)($query->sum('content_bytes') ?? 0);

        return $usedBytes + strlen((string)$article->html) <= $limit->storageBytes;
    }

    private function supportPlan(int $publicKey): string
    {
        return Yii::$container->get(SupportSettingsRepositoryInterface::class)->getForClient($publicKey)->plan;
    }

    private function categories(int $publicKey): array
    {
        return InstructionCategoryRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['parent_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    private function articles(int $publicKey): array
    {
        $query = InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC]);

        $categoryId = (int)Yii::$app->request->get('categoryId', 0);
        if ($categoryId > 0) {
            $query->andWhere(['category_id' => $categoryId]);
        }

        $status = (string)Yii::$app->request->get('status', '');
        if ($status === 'active') {
            $query->andWhere(['is_active' => true]);
        } elseif ($status === 'disabled') {
            $query->andWhere(['is_active' => false]);
        }

        return $query->all();
    }

    /**
     * @param InstructionArticleRecord[] $articles
     */
    private function commentsCount(array $articles): int
    {
        $ids = array_values(array_filter(array_map(static fn(InstructionArticleRecord $article): int => (int)$article->id, $articles)));
        if ($ids === []) {
            return 0;
        }

        return (int)InstructionArticleFeedbackRecord::find()
            ->where(['article_id' => $ids])
            ->andWhere(['<>', 'comment', ''])
            ->count();
    }

    private function isOwnerUser(): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        return (int)Yii::$app->user->id === (int)Yii::$app->user->identity->getPublicKey();
    }

    private function projectContext(): array
    {
        $projects = Yii::$container->get(ClientProjectService::class);
        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $projects->publicKeyForProject($ownerPublicKey, $projectId);

        return [$projects, $ownerPublicKey, $projectId, $publicKey];
    }

    private function projectUrl(string $path, ?int $projectId): string
    {
        return Url::to($projectId === null ? [$path] : [$path, 'projectId' => $projectId]);
    }
}
