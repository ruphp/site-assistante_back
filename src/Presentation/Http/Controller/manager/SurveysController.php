<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientProjectService;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Surveys\Domain\SurveyPlanLimit;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyFormRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionOptionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyResponseRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyRoleRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyUrlRecord;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

final class SurveysController extends ManagerController
{
    public function actionIndex(): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $limit = SurveyPlanLimit::forPlan($this->supportPlan($ownerPublicKey));
        if (!$limit->enabled) {
            return $this->render('locked', $projects->tabsData($ownerPublicKey, $projectId));
        }

        $surveys = SurveyFormRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC])
            ->all();

        return $this->render('index', [
            'surveys' => $surveys,
            'activeCount' => SurveyFormRecord::find()->where(['public_key' => $publicKey, 'is_active' => true])->count(),
            'delayedCount' => SurveyResponseRecord::find()->where(['public_key' => $publicKey, 'is_delayed' => true, 'completed_at' => null])->count(),
            'completedCount' => SurveyResponseRecord::find()->where(['public_key' => $publicKey])->andWhere(['not', ['completed_at' => null]])->count(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionCreate(): Response|string
    {
        return $this->editSurvey(null);
    }

    public function actionUpdate(int $id): Response|string
    {
        return $this->editSurvey($id);
    }

    public function actionDelete(int $id): Response
    {
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        if ($this->isOwnerUser()) {
            $projects = Yii::$container->get(ClientProjectService::class);
            $publicKey = $projects->publicKeyForProject(Yii::$app->user->identity->getPublicKey(), $projectId);
            SurveyFormRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);
            Yii::$app->session->setFlash('success', 'Анкета удалена');
        }

        return $this->redirect($this->projectUrl('/manager/surveys', $projectId));
    }

    private function editSurvey(?int $id): Response|string
    {
        if (!$this->isOwnerUser()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $limit = SurveyPlanLimit::forPlan($this->supportPlan($ownerPublicKey));
        if (!$limit->enabled) {
            return $this->redirect($this->projectUrl('/manager/surveys', $projectId));
        }

        $survey = $id === null
            ? new SurveyFormRecord(['public_key' => $publicKey, 'is_active' => true, 'sort_order' => 100, 'question_count' => null])
            : SurveyFormRecord::findOne(['id' => $id, 'public_key' => $publicKey]);

        if (!$survey instanceof SurveyFormRecord) {
            Yii::$app->session->setFlash('error', 'Анкета не найдена');

            return $this->redirect($this->projectUrl('/manager/surveys', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $this->saveSurvey($publicKey, $survey, $limit);

            return $this->redirect($this->projectUrl('/manager/surveys', $projectId));
        }

        return $this->render('form', [
            'survey' => $survey,
            'questions' => $survey->isNewRecord ? [] : SurveyQuestionRecord::find()->where(['survey_id' => $survey->id])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->all(),
            'roles' => Roles::find()->where(['public_key' => $publicKey])->orderBy(['name' => SORT_ASC])->all(),
            'selectedRoleIds' => $survey->isNewRecord ? [] : SurveyRoleRecord::find()->where(['survey_id' => $survey->id])->select('role_id')->column(),
            'surveyUrls' => $survey->isNewRecord ? [] : SurveyUrlRecord::find()->where(['survey_id' => $survey->id])->orderBy(['id' => SORT_ASC])->all(),
            'urlBindingsEnabled' => $limit->urlBindingsEnabled,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    private function saveSurvey(int $publicKey, SurveyFormRecord $survey, SurveyPlanLimit $limit): void
    {
        $data = Yii::$app->request->post('Survey');
        if (!is_array($data)) {
            return;
        }

        $survey->public_key = $publicKey;
        $survey->title = trim((string)($data['title'] ?? ''));
        $survey->is_active = (bool)($data['is_active'] ?? false);
        $survey->is_important = (bool)($data['is_important'] ?? false);
        $survey->question_count = empty($data['question_count']) ? null : max(1, (int)$data['question_count']);
        $survey->date_start = trim((string)($data['date_start'] ?? '')) ?: null;
        $survey->date_finish = trim((string)($data['date_finish'] ?? '')) ?: null;
        $survey->sort_order = (int)($data['sort_order'] ?? 100);

        if (!$survey->save()) {
            Yii::$app->session->setFlash('error', 'Не удалось сохранить анкету');

            return;
        }

        $this->saveQuestions($survey, Yii::$app->request->post('SurveyQuestion', []));
        $this->saveRoles($survey, $data);
        $this->saveUrls($publicKey, $survey, $data, $limit);
        Yii::$app->session->setFlash('success', 'Анкета сохранена');
    }

    private function saveQuestions(SurveyFormRecord $survey, mixed $data): void
    {
        SurveyQuestionRecord::deleteAll(['survey_id' => $survey->id]);
        if (!is_array($data)) {
            return;
        }

        $titles = $data['title'] ?? [];
        foreach ($titles as $index => $title) {
            $title = trim((string)$title);
            if ($title === '') {
                continue;
            }

            $type = (string)($data['type'][$index] ?? SurveyQuestionRecord::TYPE_TEXT);
            if (!in_array($type, [SurveyQuestionRecord::TYPE_TEXT, SurveyQuestionRecord::TYPE_RADIO, SurveyQuestionRecord::TYPE_CHECKBOX, SurveyQuestionRecord::TYPE_RATE], true)) {
                $type = SurveyQuestionRecord::TYPE_TEXT;
            }

            $question = new SurveyQuestionRecord([
                'survey_id' => $survey->id,
                'type' => $type,
                'title' => $title,
                'is_required' => !empty($data['is_required'][$index]),
                'is_free_answer' => !empty($data['is_free_answer'][$index]),
                'sort_order' => (int)($data['sort_order'][$index] ?? (($index + 1) * 10)),
            ]);
            if (!$question->save()) {
                continue;
            }

            $options = preg_split('/\r\n|\r|\n/', (string)($data['options'][$index] ?? '')) ?: [];
            foreach ($options as $optionIndex => $option) {
                $option = trim($option);
                if ($option === '') {
                    continue;
                }
                (new SurveyQuestionOptionRecord([
                    'question_id' => $question->id,
                    'answer' => $option,
                    'sort_order' => ($optionIndex + 1) * 10,
                ]))->save(false);
            }
        }
    }

    private function saveRoles(SurveyFormRecord $survey, array $data): void
    {
        SurveyRoleRecord::deleteAll(['survey_id' => $survey->id]);
        $roleIds = $data['role_ids'] ?? [];
        if (!is_array($roleIds)) {
            return;
        }

        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            if ($roleId <= 0) {
                continue;
            }
            (new SurveyRoleRecord(['survey_id' => $survey->id, 'role_id' => $roleId]))->save(false);
        }
    }

    private function saveUrls(int $publicKey, SurveyFormRecord $survey, array $data, SurveyPlanLimit $limit): void
    {
        if (!$limit->urlBindingsEnabled) {
            return;
        }

        SurveyUrlRecord::deleteAll(['survey_id' => $survey->id]);
        $rawUrls = preg_split('/\r\n|\r|\n/', (string)($data['urls'] ?? '')) ?: [];
        foreach ($rawUrls as $rawUrl) {
            $rawUrl = trim($rawUrl);
            if ($rawUrl === '') {
                continue;
            }

            [$url, $children, $query] = array_pad(array_map('trim', explode('|', $rawUrl)), 3, '');
            (new SurveyUrlRecord([
                'survey_id' => $survey->id,
                'public_key' => $publicKey,
                'url' => $url,
                'include_children' => in_array($children, ['1', 'yes', 'true', 'children'], true),
                'include_query' => in_array($query, ['1', 'yes', 'true', 'query'], true),
            ]))->save(false);
        }
    }

    private function supportPlan(int $publicKey): string
    {
        return Yii::$container->get(SupportSettingsRepositoryInterface::class)->getForClient($publicKey)->plan;
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
