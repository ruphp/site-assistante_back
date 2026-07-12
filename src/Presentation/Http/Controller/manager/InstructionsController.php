<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientProjectService;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionArticleRecord;
use app\Modules\Instructions\Infrastructure\YiiActiveRecord\InstructionCategoryRecord;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\web\Response;
use yii\helpers\Url;

final class InstructionsController extends ManagerController
{
    public function actionIndex(): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        $projects = Yii::$container->get(ClientProjectService::class);
        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $projects->publicKeyForProject($ownerPublicKey, $projectId);

        if (Yii::$app->request->isPost) {
            $this->saveCategory($publicKey);
            $this->saveArticle($publicKey);

            return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
        }

        return $this->render('index', [
            'categories' => $this->categories($publicKey),
            'articles' => $this->articles($publicKey),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionDeleteCategory(int $id): Response
    {
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        if ($this->isOwnerUser()) {
            $projects = Yii::$container->get(ClientProjectService::class);
            $publicKey = $projects->publicKeyForProject(Yii::$app->user->identity->getPublicKey(), $projectId);
            InstructionCategoryRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);
            Yii::$app->session->setFlash('success', 'Категория удалена');
        }

        return $this->redirect($this->projectUrl('/manager/instructions', $projectId));
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

    private function saveCategory(int $publicKey): void
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

        $category->name = trim((string)($data['name'] ?? ''));
        $category->parent_id = empty($data['parent_id']) ? null : (int)$data['parent_id'];
        $category->sort_order = (int)($data['sort_order'] ?? 100);
        $category->is_active = (bool)($data['is_active'] ?? false);

        if ($category->save()) {
            Yii::$app->session->setFlash('success', 'Категория сохранена');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось сохранить категорию');
        }
    }

    private function saveArticle(int $publicKey): void
    {
        $data = Yii::$app->request->post('InstructionArticle');
        if (!is_array($data)) {
            return;
        }

        $article = empty($data['id'])
            ? new InstructionArticleRecord(['public_key' => $publicKey])
            : InstructionArticleRecord::findOne(['id' => (int)$data['id'], 'public_key' => $publicKey]);

        if (!$article instanceof InstructionArticleRecord) {
            return;
        }

        $article->title = trim((string)($data['title'] ?? ''));
        $article->html = (string)($data['html'] ?? '');
        $article->category_id = empty($data['category_id']) ? null : (int)$data['category_id'];
        $article->sort_order = (int)($data['sort_order'] ?? 100);
        $article->is_active = (bool)($data['is_active'] ?? false);

        if ($article->save()) {
            Yii::$app->session->setFlash('success', 'Инструкция сохранена');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось сохранить инструкцию');
        }
    }

    private function categories(int $publicKey): array
    {
        return InstructionCategoryRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
    }

    private function articles(int $publicKey): array
    {
        return InstructionArticleRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC])
            ->all();
    }

    private function isOwnerUser(): bool
    {
        if (Yii::$app->user->isGuest) {
            return false;
        }

        return (int)Yii::$app->user->id === (int)Yii::$app->user->identity->getPublicKey();
    }

    private function projectUrl(string $path, ?int $projectId): string
    {
        return Url::to($projectId === null ? [$path] : [$path, 'projectId' => $projectId]);
    }
}
