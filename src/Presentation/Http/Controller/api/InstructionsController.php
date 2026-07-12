<?php

namespace app\Presentation\Http\Controller\api;

use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

final class InstructionsController extends ApiController
{
    public function actionCourses(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $query = trim((string)Yii::$app->request->get('query', ''));

        return $this->tree($publicKey, $query);
    }

    public function actionCourse(int $publicKey, int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $article = InstructionArticleRecord::find()
            ->where(['id' => $id, 'public_key' => $publicKey, 'is_active' => true])
            ->one();

        if (!$article instanceof InstructionArticleRecord) {
            return $this->HTTPStatus(404, 'Инструкция не найдена');
        }

        return [
            'id' => (string)$article->id,
            'name' => $article->title,
            'parent_id' => $article->category_id === null ? null : (string)$article->category_id,
            'is_course' => true,
            'html' => $article->html,
            'views' => 0,
            'like' => 0,
            'dislike' => 0,
            'tags' => [],
            'urls' => [],
        ];
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

    public function actionFavorites(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    public function actionCourseEstimate(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->HTTPStatus(200, 'Ok');
    }

    public function actionLogInterest(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $this->HTTPStatus(200, 'Ok');
    }

    private function tree(int $publicKey, string $query = ''): array
    {
        $categories = InstructionCategoryRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true])
            ->orderBy(['parent_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $articlesQuery = InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true]);

        if ($query !== '') {
            $articlesQuery->andWhere([
                'or',
                ['ilike', 'title', $query],
                ['ilike', 'html', $query],
            ]);
        }

        $articles = $articlesQuery
            ->orderBy(['category_id' => SORT_ASC, 'sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        $usedCategoryIds = [];
        foreach ($articles as $article) {
            if ($article->category_id !== null) {
                $usedCategoryIds[(int)$article->category_id] = true;
            }
        }

        $result = [];
        foreach ($categories as $category) {
            if ($query !== '' && !isset($usedCategoryIds[(int)$category->id])) {
                continue;
            }

            $result[] = [
                'id' => $this->categoryNodeId((int)$category->id),
                'name' => $category->name,
                'parent_id' => $category->parent_id === null ? null : $this->categoryNodeId((int)$category->parent_id),
                'is_course' => false,
                'html' => '',
                'urls' => [],
            ];
        }

        foreach ($articles as $article) {
            $result[] = [
                'id' => (string)$article->id,
                'name' => $article->title,
                'parent_id' => $article->category_id === null ? null : $this->categoryNodeId((int)$article->category_id),
                'is_course' => true,
                'html' => $article->html,
                'urls' => [],
            ];
        }

        return $result;
    }

    private function categoryNodeId(int $id): string
    {
        return 'category-' . $id;
    }
}
