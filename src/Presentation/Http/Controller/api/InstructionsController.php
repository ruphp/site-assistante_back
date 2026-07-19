<?php

namespace app\Presentation\Http\Controller\api;

use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFavoriteRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleFeedbackRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRoleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleUrlRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

final class InstructionsController extends ApiController
{
    public function actionInstructions(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->tree($publicKey, trim((string)Yii::$app->request->get('query', '')));
    }

    public function actionInstruction(int $publicKey, int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $article = $this->findVisibleArticle($publicKey, $id);

        if (!$article instanceof InstructionArticleRecord) {
            return $this->HTTPStatus(404, 'Инструкция не найдена');
        }

        $article->updateCounters(['views' => 1]);
        $article->refresh();

        return $this->articlePayload($article, true);
    }

    public function actionSearch(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $word = trim(strip_tags((string)Yii::$app->request->get('word', '')));

        return array_values(array_filter(
            $this->tree($publicKey, $word),
            static fn(array $item): bool => (bool)$item['is_course'],
        ));
    }

    public function actionLinktag(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    public function actionFavorites(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $addId = (int)Yii::$app->request->get('instruction_add', 0);
        $deleteId = (int)Yii::$app->request->get('instruction_delete', 0);
        $articleId = (int)Yii::$app->request->get('id_source', Yii::$app->request->get('instruction_id', $addId ?: $deleteId));
        $visitorKey = $this->visitorKey();

        if ($articleId <= 0) {
            $favoriteIds = InstructionArticleFavoriteRecord::find()
                ->where(['public_key' => $publicKey, 'visitor_key' => $visitorKey])
                ->select('article_id')
                ->column();

            if ($favoriteIds === []) {
                return [];
            }

            return array_map(
                fn(InstructionArticleRecord $article): array => $this->articlePayload($article, false),
                InstructionArticleRecord::find()
                    ->where(['public_key' => $publicKey, 'id' => $favoriteIds, 'is_active' => true, 'admin_blocked' => false])
                    ->orderBy(['title' => SORT_ASC])
                    ->all(),
            );
        }

        $favorite = InstructionArticleFavoriteRecord::findOne([
            'public_key' => $publicKey,
            'article_id' => $articleId,
            'visitor_key' => $visitorKey,
        ]);

        if ($deleteId > 0 && $favorite instanceof InstructionArticleFavoriteRecord) {
            $favorite->delete();

            return ['favorite' => false];
        }

        if ($favorite instanceof InstructionArticleFavoriteRecord) {
            return ['favorite' => true];
        }

        $favorite = new InstructionArticleFavoriteRecord([
            'public_key' => $publicKey,
            'article_id' => $articleId,
            'visitor_id' => (int)Yii::$app->request->get('userId', 0),
            'visitor_key' => $visitorKey,
        ]);
        $favorite->save(false);

        return ['favorite' => true];
    }

    public function actionInstructionEstimate(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $articleId = (int)Yii::$app->request->get('id_source', 0);
        $isLike = (int)Yii::$app->request->get('up', 1) === 1;
        $comment = trim((string)Yii::$app->request->get('comment', ''));
        $visitorKey = $this->visitorKey();
        $article = $this->findVisibleArticle($publicKey, $articleId);

        if (!$article instanceof InstructionArticleRecord) {
            return $this->HTTPStatus(404, 'Инструкция не найдена');
        }

        $feedback = InstructionArticleFeedbackRecord::findOne([
            'public_key' => $publicKey,
            'article_id' => $articleId,
            'visitor_key' => $visitorKey,
        ]);

        if (!$feedback instanceof InstructionArticleFeedbackRecord) {
            $feedback = new InstructionArticleFeedbackRecord([
                'public_key' => $publicKey,
                'article_id' => $articleId,
                'visitor_id' => (int)Yii::$app->request->get('userId', 0),
                'visitor_key' => $visitorKey,
            ]);
        }

        $oldValue = $feedback->isNewRecord ? null : (bool)$feedback->is_like;
        $feedback->is_like = $isLike;
        if ($comment !== '') {
            $feedback->comment = $comment;
        }
        $feedback->save(false);

        if ($oldValue === null) {
            $article->updateCounters([$isLike ? 'likes' : 'dislikes' => 1]);
        } elseif ($oldValue !== $isLike) {
            $article->updateCounters([
                $isLike ? 'likes' : 'dislikes' => 1,
                $isLike ? 'dislikes' : 'likes' => -1,
            ]);
        }

        return ['id' => $feedback->id];
    }

    public function actionLogInterest(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->HTTPStatus(200, 'Ok');
    }

    private function tree(int $publicKey, string $query = ''): array
    {
        $categories = InstructionCategoryRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true, 'admin_blocked' => false])
            ->orderBy(['parent_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        $categoriesById = [];
        foreach ($categories as $category) {
            $categoriesById[(int)$category->id] = $category;
        }

        $articlesQuery = InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true, 'admin_blocked' => false]);

        if ($query !== '') {
            $articlesQuery->andWhere([
                'or',
                ['ilike', 'title', $query],
                ['ilike', 'html', $query],
            ]);
        }

        $articles = array_values(array_filter(
            $articlesQuery->orderBy(['category_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'id' => SORT_ASC])->all(),
            fn(InstructionArticleRecord $article): bool => $this->articleAllowedForVisitor($article),
        ));

        $usedCategoryIds = [];
        foreach ($articles as $article) {
            $categoryId = $article->category_id === null ? null : (int)$article->category_id;
            while ($categoryId !== null && isset($categoriesById[$categoryId])) {
                $usedCategoryIds[$categoryId] = true;
                $parentId = $categoriesById[$categoryId]->parent_id;
                $categoryId = $parentId === null ? null : (int)$parentId;
            }
        }

        $result = [];
        foreach ($categories as $category) {
            if (!isset($usedCategoryIds[(int)$category->id])) {
                continue;
            }

            $result[] = [
                'id' => $this->categoryNodeId((int)$category->id),
                'name' => $category->name,
                'parent_id' => $category->parent_id === null ? null : $this->categoryNodeId((int)$category->parent_id),
                'is_course' => false,
                'html' => '',
                'sort_order' => (int)$category->sort_order,
                'order' => (int)$category->sort_order,
                'urls' => [],
            ];
        }

        foreach ($articles as $article) {
            $result[] = $this->articlePayload($article, false);
        }

        usort($result, static function (array $a, array $b): int {
            $byParent = strcmp((string)($a['parent_id'] ?? ''), (string)($b['parent_id'] ?? ''));
            if ($byParent !== 0) {
                return $byParent;
            }

            $byOrder = (int)($a['sort_order'] ?? $a['order'] ?? 100) <=> (int)($b['sort_order'] ?? $b['order'] ?? 100);
            if ($byOrder !== 0) {
                return $byOrder;
            }

            return strcmp((string)($a['name'] ?? ''), (string)($b['name'] ?? ''));
        });

        return $result;
    }

    private function findVisibleArticle(int $publicKey, int $id): ?InstructionArticleRecord
    {
        $article = InstructionArticleRecord::find()
            ->where(['id' => $id, 'public_key' => $publicKey, 'is_active' => true, 'admin_blocked' => false])
            ->one();

        if (!$article instanceof InstructionArticleRecord || !$this->articleAllowedForVisitor($article)) {
            return null;
        }

        return $article;
    }

    private function articleAllowedForVisitor(InstructionArticleRecord $article): bool
    {
        $roles = InstructionArticleRoleRecord::find()
            ->where(['article_id' => $article->id])
            ->select('role_id')
            ->column();
        if ($roles !== []) {
            $visitorRoles = $this->visitorRoles();
            if ($visitorRoles === [] || array_intersect(array_map('intval', $roles), $visitorRoles) === []) {
                return false;
            }
        }

        $urls = $this->articleUrls($article);
        if ($urls === []) {
            return true;
        }

        $pageUrl = (string)Yii::$app->request->get('pageUrl', '');
        $pathname = (string)Yii::$app->request->get('pathname', '');

        foreach ($urls as $url) {
            if ($this->urlMatches($url, $pageUrl, $pathname)) {
                return true;
            }
        }

        return false;
    }

    private function articlePayload(InstructionArticleRecord $article, bool $withHtml): array
    {
        return [
            'id' => (string)$article->id,
            'name' => $article->title,
            'parent_id' => $article->category_id === null ? null : $this->categoryNodeId((int)$article->category_id),
            'is_course' => true,
            'html' => $withHtml ? $article->html : '',
            'sort_order' => (int)$article->sort_order,
            'order' => (int)$article->sort_order,
            'views' => (int)$article->views,
            'like' => (int)$article->likes,
            'dislike' => (int)$article->dislikes,
            'tags' => [],
            'urls' => array_map(
                static fn(InstructionArticleUrlRecord $url): array => [
                    'url' => $url->url,
                    'is_children' => (bool)$url->include_children,
                    'is_get' => (bool)$url->include_query,
                ],
                $this->articleUrls($article),
            ),
        ];
    }

    /**
     * @return InstructionArticleUrlRecord[]
     */
    private function articleUrls(InstructionArticleRecord $article): array
    {
        return InstructionArticleUrlRecord::find()
            ->where(['article_id' => $article->id])
            ->orderBy(['id' => SORT_ASC])
            ->all();
    }

    private function urlMatches(InstructionArticleUrlRecord $url, string $pageUrl, string $pathname): bool
    {
        $target = trim($url->url);
        if ($target === '') {
            return false;
        }

        $current = $url->include_query ? $pageUrl : strtok($pageUrl, '?');
        if ($current === false || $current === '') {
            $current = $pathname;
        }

        if ($url->include_children) {
            return strpos($current, $target) === 0;
        }

        return rtrim($current, '/') === rtrim($target, '/');
    }

    private function visitorRoles(): array
    {
        $role = Yii::$app->request->get('userRole', []);
        if (is_string($role)) {
            $role = trim($role);
            if ($role === '') {
                return [];
            }
            $role = str_contains($role, ',') ? explode(',', $role) : [$role];
        }
        if (!is_array($role)) {
            return [];
        }

        return array_values(array_filter(array_map('intval', $role)));
    }

    private function visitorKey(): string
    {
        $userId = (string)Yii::$app->request->get('userId', '');
        if ($userId !== '' && $userId !== '0') {
            return 'user:' . $userId;
        }

        return 'anon:' . sha1((string)Yii::$app->request->userIP . '|' . (string)Yii::$app->request->userAgent);
    }

    private function categoryNodeId(int $id): string
    {
        return 'category-' . $id;
    }
}
