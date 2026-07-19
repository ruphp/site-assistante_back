<?php

namespace app\Presentation\Http\Controller\api;

use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

final class InstructionsController extends ApiController
{
    public function actionInstructions($publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $publicKey = (int)$publicKey;

        $categories = $this->categories($publicKey);
        $articles = $this->articles($publicKey);

        return array_merge($categories, $articles);
    }

    public function actionInstruction($publicKey, $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $article = Yii::$app->db->createCommand(
            'SELECT id, category_id, title, html, sort_order, views, likes, dislikes
               FROM sw_instruction_articles
              WHERE id = :id
                AND public_key = :public_key
                AND is_active = TRUE
                AND COALESCE(admin_blocked, FALSE) = FALSE
              LIMIT 1',
            [':id' => (int)$id, ':public_key' => (int)$publicKey],
        )->queryOne();

        if (!$article) {
            return $this->HTTPStatus(404, 'Instruction not found');
        }

        Yii::$app->db->createCommand(
            'UPDATE sw_instruction_articles SET views = COALESCE(views, 0) + 1 WHERE id = :id',
            [':id' => (int)$article['id']],
        )->execute();

        return $this->articleRow($article, true);
    }

    public function actionSearch($publicKey, $word = ''): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $word = trim((string)$word);

        if ($word === '') {
            return [];
        }

        $rows = Yii::$app->db->createCommand(
            'SELECT id, category_id, title, html, sort_order, views, likes, dislikes
               FROM sw_instruction_articles
              WHERE public_key = :public_key
                AND is_active = TRUE
                AND COALESCE(admin_blocked, FALSE) = FALSE
                AND (title ILIKE :word OR html ILIKE :word)
              ORDER BY sort_order ASC, id ASC
              LIMIT 30',
            [':public_key' => (int)$publicKey, ':word' => '%' . $word . '%'],
        )->queryAll();

        return array_map(fn(array $row): array => $this->articleRow($row, false), $rows);
    }

    public function actionLinktag(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    public function actionFavorites($publicKey, $userId = null): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $publicKey = (int)$publicKey;
        $visitorKey = $this->visitorKey($userId);

        $addId = Yii::$app->request->get('instruction_add');
        if ($addId !== null) {
            Yii::$app->db->createCommand(
                'INSERT INTO sw_instruction_article_favorites (public_key, article_id, visitor_id, visitor_key, created_at)
                 VALUES (:public_key, :article_id, :visitor_id, :visitor_key, :created_at)
                 ON CONFLICT DO NOTHING',
                [
                    ':public_key' => $publicKey,
                    ':article_id' => (int)$addId,
                    ':visitor_id' => is_numeric($userId) ? (int)$userId : null,
                    ':visitor_key' => $visitorKey,
                    ':created_at' => time(),
                ],
            )->execute();

            return [];
        }

        $deleteId = Yii::$app->request->get('instruction_delete');
        if ($deleteId !== null) {
            Yii::$app->db->createCommand(
                'DELETE FROM sw_instruction_article_favorites
                  WHERE public_key = :public_key AND article_id = :article_id AND visitor_key = :visitor_key',
                [':public_key' => $publicKey, ':article_id' => (int)$deleteId, ':visitor_key' => $visitorKey],
            )->execute();

            return [];
        }

        $rows = Yii::$app->db->createCommand(
            'SELECT a.id, a.category_id, a.title, a.html, a.sort_order, a.views, a.likes, a.dislikes
               FROM sw_instruction_article_favorites f
               JOIN sw_instruction_articles a ON a.id = f.article_id
              WHERE f.public_key = :public_key
                AND f.visitor_key = :visitor_key
                AND a.is_active = TRUE
                AND COALESCE(a.admin_blocked, FALSE) = FALSE
              ORDER BY f.created_at DESC',
            [':public_key' => $publicKey, ':visitor_key' => $visitorKey],
        )->queryAll();

        return array_map(fn(array $row): array => $this->articleRow($row, false), $rows);
    }

    public function actionInstructionEstimate($publicKey, $id_source, $up = null, $like = null, $comment = null): int|array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $publicKey = (int)$publicKey;
        $articleId = (int)$id_source;

        if ($like !== null && $comment !== null) {
            Yii::$app->db->createCommand(
                'UPDATE sw_instruction_article_feedback SET comment = :comment WHERE id = :id AND public_key = :public_key',
                [':comment' => (string)$comment, ':id' => (int)$like, ':public_key' => $publicKey],
            )->execute();

            return [];
        }

        $isLike = (int)$up === 1;
        Yii::$app->db->createCommand(
            'INSERT INTO sw_instruction_article_feedback (public_key, article_id, visitor_id, visitor_key, is_like, created_at)
             VALUES (:public_key, :article_id, :visitor_id, :visitor_key, :is_like, :created_at)',
            [
                ':public_key' => $publicKey,
                ':article_id' => $articleId,
                ':visitor_id' => is_numeric(Yii::$app->request->get('userId')) ? (int)Yii::$app->request->get('userId') : null,
                ':visitor_key' => $this->visitorKey(Yii::$app->request->get('userId')),
                ':is_like' => $isLike,
                ':created_at' => time(),
            ],
        )->execute();
        $feedbackId = (int)Yii::$app->db->getLastInsertID();

        Yii::$app->db->createCommand(
            'UPDATE sw_instruction_articles
                SET likes = COALESCE(likes, 0) + :likes,
                    dislikes = COALESCE(dislikes, 0) + :dislikes
              WHERE id = :id AND public_key = :public_key',
            [
                ':likes' => $isLike ? 1 : 0,
                ':dislikes' => $isLike ? 0 : 1,
                ':id' => $articleId,
                ':public_key' => $publicKey,
            ],
        )->execute();

        return $feedbackId;
    }

    public function actionLogInterest(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [];
    }

    private function categories(int $publicKey): array
    {
        $rows = Yii::$app->db->createCommand(
            'SELECT id, parent_id, name, sort_order
               FROM sw_instruction_categories
              WHERE public_key = :public_key
                AND is_active = TRUE
                AND COALESCE(admin_blocked, FALSE) = FALSE
              ORDER BY sort_order ASC, id ASC',
            [':public_key' => $publicKey],
        )->queryAll();

        return array_map(static function (array $row): array {
            return [
                'id' => 'category-' . $row['id'],
                'name' => (string)$row['name'],
                'parent_id' => $row['parent_id'] === null ? null : 'category-' . $row['parent_id'],
                'is_course' => false,
                'html' => '',
                'sort_order' => (int)$row['sort_order'],
                'order' => (int)$row['sort_order'],
                'urls' => [],
            ];
        }, $rows);
    }

    private function articles(int $publicKey): array
    {
        $rows = Yii::$app->db->createCommand(
            'SELECT id, category_id, title, html, sort_order, views, likes, dislikes
               FROM sw_instruction_articles
              WHERE public_key = :public_key
                AND is_active = TRUE
                AND COALESCE(admin_blocked, FALSE) = FALSE
              ORDER BY sort_order ASC, id ASC',
            [':public_key' => $publicKey],
        )->queryAll();

        return array_map(fn(array $row): array => $this->articleRow($row, false), $rows);
    }

    private function articleRow(array $row, bool $withHtml): array
    {
        $id = (int)$row['id'];

        return [
            'id' => (string)$id,
            'name' => (string)$row['title'],
            'parent_id' => $row['category_id'] === null ? null : 'category-' . $row['category_id'],
            'is_course' => true,
            'html' => $withHtml ? (string)$row['html'] : '',
            'sort_order' => (int)$row['sort_order'],
            'order' => (int)$row['sort_order'],
            'views' => (int)($row['views'] ?? 0),
            'like' => (int)($row['likes'] ?? 0),
            'dislike' => (int)($row['dislikes'] ?? 0),
            'tags' => [],
            'urls' => $this->articleUrls($id),
        ];
    }

    private function articleUrls(int $articleId): array
    {
        $rows = Yii::$app->db->createCommand(
            'SELECT url, include_children, include_query
               FROM sw_instruction_article_urls
              WHERE article_id = :article_id
              ORDER BY id ASC',
            [':article_id' => $articleId],
        )->queryAll();

        return array_map(static fn(array $row): array => [
            'url' => (string)$row['url'],
            'is_children' => (bool)$row['include_children'],
            'is_get' => (bool)$row['include_query'],
        ], $rows);
    }

    private function visitorKey(mixed $userId): string
    {
        if ($userId !== null && $userId !== '') {
            return 'user:' . (string)$userId;
        }

        return 'ip:' . (Yii::$app->request->userIP ?? '0.0.0.0');
    }
}
