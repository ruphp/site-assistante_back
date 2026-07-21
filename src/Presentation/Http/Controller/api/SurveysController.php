<?php

namespace app\Presentation\Http\Controller\api;

use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyFormRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionOptionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyQuestionRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyResponseRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyRoleRecord;
use app\Modules\Surveys\Infrastructure\YiiActiveRecord\SurveyUrlRecord;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

final class SurveysController extends ApiController
{
    public function actionListPolls(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return array_values(array_filter(
            array_map(fn(SurveyFormRecord $survey): array => $this->surveyPayload($survey), $this->activeSurveys($publicKey)),
            fn(array $survey): bool => !(bool)$survey['is_delayed'],
        ));
    }

    public function actionDelayedPolls(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $visitorKey = $this->visitorKey();
        $surveyIds = SurveyResponseRecord::find()
            ->where(['public_key' => $publicKey, 'visitor_key' => $visitorKey, 'is_delayed' => true])
            ->andWhere(['completed_at' => null])
            ->select('survey_id')
            ->column();

        if ($surveyIds === []) {
            return [];
        }

        $rows = SurveyFormRecord::find()
            ->where(['id' => $surveyIds, 'public_key' => $publicKey, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return array_map(fn(SurveyFormRecord $survey): array => $this->surveyPayload($survey), $rows);
    }

    public function actionPoll(int $publicKey, int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $survey = SurveyFormRecord::findOne(['id' => $id, 'public_key' => $publicKey, 'is_active' => true]);
        if (!$survey instanceof SurveyFormRecord || (!$this->surveyAllowed($publicKey, $survey, false) && !$this->isDelayedForVisitor($publicKey, $id))) {
            return $this->HTTPStatus(404, 'Survey not found');
        }

        return $this->surveyPayload($survey);
    }

    public function actionEndPoll(int $publicKey, int $id): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $survey = SurveyFormRecord::findOne(['id' => $id, 'public_key' => $publicKey, 'is_active' => true]);
        if (!$survey instanceof SurveyFormRecord) {
            return $this->HTTPStatus(404, 'Survey not found');
        }

        $answers = $this->requestAnswers();
        $response = $this->response($publicKey, (int)$survey->id);
        $response->roles_json = json_encode($this->requestRoles(), JSON_UNESCAPED_UNICODE);
        $response->answers_json = json_encode($answers, JSON_UNESCAPED_UNICODE);
        $response->is_delayed = $answers === [];
        $response->completed_at = $answers === [] ? null : date('Y-m-d H:i:s');
        $response->updated_at = date('Y-m-d H:i:s');
        $response->save(false);

        return ['ok' => true];
    }

    /**
     * @return SurveyFormRecord[]
     */
    private function activeSurveys(int $publicKey): array
    {
        $today = date('Y-m-d');
        $rows = SurveyFormRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true])
            ->andWhere(['or', ['date_start' => null], ['<=', 'date_start', $today]])
            ->andWhere(['or', ['date_finish' => null], ['>=', 'date_finish', $today]])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();

        return array_values(array_filter(
            $rows,
            fn(SurveyFormRecord $survey): bool => $this->hasQuestions($survey) && $this->surveyAllowed($publicKey, $survey, true),
        ));
    }

    private function hasQuestions(SurveyFormRecord $survey): bool
    {
        return SurveyQuestionRecord::find()->where(['survey_id' => $survey->id])->exists();
    }

    private function surveyAllowed(int $publicKey, SurveyFormRecord $survey, bool $skipCompleted): bool
    {
        if (!$this->rolesAllowed($publicKey, SurveyRoleRecord::find()->where(['survey_id' => $survey->id])->select('role_id')->column())) {
            return false;
        }

        if (!$this->urlAllowed((int)$survey->id)) {
            return false;
        }

        if (!$skipCompleted) {
            return true;
        }

        $response = SurveyResponseRecord::findOne([
            'public_key' => $publicKey,
            'survey_id' => $survey->id,
            'visitor_key' => $this->visitorKey(),
        ]);

        return !$response instanceof SurveyResponseRecord || $response->completed_at === null;
    }

    private function surveyPayload(SurveyFormRecord $survey): array
    {
        $questions = SurveyQuestionRecord::find()
            ->where(['survey_id' => $survey->id])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        $response = SurveyResponseRecord::findOne([
            'public_key' => (int)$survey->public_key,
            'survey_id' => (int)$survey->id,
            'visitor_key' => $this->visitorKey(),
        ]);

        return [
            'id' => (int)$survey->id,
            'title' => (string)$survey->title,
            'public_key' => (int)$survey->public_key,
            'is_important' => (int)(bool)$survey->is_important,
            'question_count' => $survey->question_count === null ? null : (int)$survey->question_count,
            'is_delayed' => $response instanceof SurveyResponseRecord && (bool)$response->is_delayed && $response->completed_at === null,
            'surveys' => array_map(fn(SurveyQuestionRecord $question, int $index): array => $this->questionPayload($question, $index), $questions, array_keys($questions)),
        ];
    }

    private function isDelayedForVisitor(int $publicKey, int $surveyId): bool
    {
        return SurveyResponseRecord::find()
            ->where([
                'public_key' => $publicKey,
                'survey_id' => $surveyId,
                'visitor_key' => $this->visitorKey(),
                'is_delayed' => true,
                'completed_at' => null,
            ])
            ->exists();
    }

    private function questionPayload(SurveyQuestionRecord $question, int $index): array
    {
        return [
            'id' => (int)$question->id,
            'index' => $index,
            'poll_id' => (int)$question->survey_id,
            'type' => (string)$question->type,
            'title' => (string)$question->title,
            'required' => (bool)$question->is_required,
            'is_free_answer' => (bool)$question->is_free_answer,
            'data' => array_map(
                fn(SurveyQuestionOptionRecord $option): array => [
                    'id' => (int)$option->id,
                    'survey' => (int)$question->survey_id,
                    'answer' => (string)$option->answer,
                ],
                SurveyQuestionOptionRecord::find()->where(['question_id' => $question->id])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->all(),
            ),
        ];
    }

    private function urlAllowed(int $surveyId): bool
    {
        $urls = SurveyUrlRecord::find()->where(['survey_id' => $surveyId])->all();
        if ($urls === []) {
            return true;
        }

        foreach ($urls as $url) {
            if ($this->urlMatches((string)$url->url, (bool)$url->include_children, (bool)$url->include_query)) {
                return true;
            }
        }

        return false;
    }

    private function urlMatches(string $target, bool $includeChildren, bool $includeQuery): bool
    {
        $target = trim($target);
        if ($target === '') {
            return true;
        }

        $pageUrl = (string)Yii::$app->request->get('pageUrl', '');
        $pathname = (string)Yii::$app->request->get('pathname', '');
        $getParams = (string)Yii::$app->request->get('getparams', '');
        $isAbsoluteTarget = preg_match('~^https?://~i', $target) === 1;
        $current = $isAbsoluteTarget && $pageUrl !== '' ? $pageUrl : $pathname . ($includeQuery ? $getParams : '');
        if (!$includeQuery) {
            $current = strtok($current, '?') ?: $current;
        }

        return $includeChildren
            ? strpos(rtrim($current, '/'), rtrim($target, '/')) === 0
            : rtrim($current, '/') === rtrim($target, '/');
    }

    private function rolesAllowed(int $publicKey, array $roleIds): bool
    {
        $roleIds = array_values(array_filter(array_map('intval', $roleIds)));
        if ($roleIds === []) {
            return true;
        }

        $visitorRoles = $this->visitorRoleIds($publicKey);

        return $visitorRoles !== [] && array_intersect($roleIds, $visitorRoles) !== [];
    }

    private function visitorRoleIds(int $publicKey): array
    {
        $raw = Yii::$app->request->get('userRole', []);
        if (is_string($raw)) {
            $raw = trim($raw) === '' ? [] : explode(',', $raw);
        }
        if (!is_array($raw)) {
            return [];
        }

        $rawIds = array_values(array_filter(array_map('intval', $raw)));
        if ($rawIds === []) {
            return [];
        }

        $mappedIds = Roles::find()
            ->where(['public_key' => $publicKey])
            ->andWhere(['id_role_in_system' => $rawIds])
            ->select('id')
            ->column();

        return array_values(array_unique(array_merge($rawIds, array_map('intval', $mappedIds))));
    }

    private function response(int $publicKey, int $surveyId): SurveyResponseRecord
    {
        $visitorKey = $this->visitorKey();
        $response = SurveyResponseRecord::findOne([
            'public_key' => $publicKey,
            'survey_id' => $surveyId,
            'visitor_key' => $visitorKey,
        ]);

        if ($response instanceof SurveyResponseRecord) {
            return $response;
        }

        return new SurveyResponseRecord([
            'public_key' => $publicKey,
            'survey_id' => $surveyId,
            'visitor_key' => $visitorKey,
            'visitor_id' => Yii::$app->request->get('visitorId'),
            'user_id' => is_numeric(Yii::$app->request->get('userId')) ? (int)Yii::$app->request->get('userId') : null,
            'roles_json' => json_encode([], JSON_UNESCAPED_UNICODE),
            'answers_json' => json_encode([], JSON_UNESCAPED_UNICODE),
            'is_delayed' => false,
        ]);
    }

    private function requestAnswers(): array
    {
        $answers = Yii::$app->request->get('answers', Yii::$app->request->post('answers', null));
        if ($answers === null) {
            $rawBody = Yii::$app->request->rawBody;
            if ($rawBody !== '') {
                $body = json_decode($rawBody, true);
                $answers = is_array($body) ? ($body['answers'] ?? null) : null;
            }
        }

        return is_array($answers) ? $answers : [];
    }

    private function requestRoles(): array
    {
        $roles = Yii::$app->request->get('userRole', []);
        if (is_string($roles)) {
            $roles = trim($roles) === '' ? [] : explode(',', $roles);
        }

        return is_array($roles) ? array_values(array_map('intval', $roles)) : [];
    }

    private function visitorKey(): string
    {
        $userId = (string)Yii::$app->request->get('userId', '');
        if ($userId !== '' && $userId !== '0') {
            return 'user:' . $userId;
        }

        return 'anon:' . sha1((string)Yii::$app->request->get('visitorId', Yii::$app->request->userIP ?? ''));
    }
}
