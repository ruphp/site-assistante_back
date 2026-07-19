<?php

namespace app\Presentation\Http\Controller\api;

use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRoleRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintUrlRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingProgressRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRoleRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingStepRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSelectorSessionRecord;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

final class OnboardingController extends ApiController
{
    public function actionHints(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return array_map(
            fn(OnboardingHintRecord $hint): array => $this->hintPayload($hint),
            array_values(array_filter($this->activeHints($publicKey), fn(OnboardingHintRecord $hint): bool => (bool)$hint->standalone_enabled)),
        );
    }

    public function actionOnboardings(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = (int)Yii::$app->request->get('id', 0);
        $requestedStartStep = $id > 0 ? $this->requestedStartStep() : null;
        $query = OnboardingRecord::find()
            ->where(['public_key' => $publicKey, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);

        if ($id > 0) {
            $query->andWhere(['id' => $id]);
        } else {
            $query->andWhere(['auto_start' => true]);
        }

        $result = [];
        foreach ($query->all() as $onboarding) {
            if (!$this->rolesAllowed($publicKey, OnboardingRoleRecord::find()->where(['onboarding_id' => $onboarding->id])->select('role_id')->column())) {
                continue;
            }

            $allSections = $this->activeSectionsWithSteps($onboarding);
            $sections = $this->visibleSections($allSections);
            if ($sections === []) {
                continue;
            }

            $progress = $this->progress($publicKey, (int)$onboarding->id);
            if ($progress->is_finished && $id <= 0) {
                continue;
            }

            $stepsCount = $this->stepsCount($onboarding);
            $countViewed = min($stepsCount, max(0, (int)$progress->count_viewed));
            $resumeCountViewed = $countViewed >= $stepsCount ? 0 : $countViewed;
            $result[] = [
                'id' => (int)$onboarding->id,
                'public_key' => (int)$onboarding->public_key,
                'timeout' => (int)$onboarding->timeout,
                'title' => (string)$onboarding->title,
                'type' => (int)$onboarding->type,
                'auto_start' => (bool)$onboarding->auto_start,
                'repeat_count' => 0,
                'data' => array_map(
                    fn(OnboardingSectionRecord $section): array => $this->sectionPayload(
                        $section,
                        $this->nextSectionUrl($allSections, $section),
                        $requestedStartStep ?? $this->startStepForSection($allSections, $section, $resumeCountViewed)
                    ),
                    $sections
                ),
                'is_blur' => (bool)$onboarding->is_blur ? 1 : 0,
                'count_unviewed' => max(0, $stepsCount - $countViewed),
                'count_viewed' => $countViewed,
            ];
        }

        return $result;
    }

    public function actionAllonboardings(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = [];

        foreach (OnboardingRecord::find()->where(['public_key' => $publicKey, 'is_active' => true])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->all() as $onboarding) {
            if (!$this->rolesAllowed($publicKey, OnboardingRoleRecord::find()->where(['onboarding_id' => $onboarding->id])->select('role_id')->column())) {
                continue;
            }

            $sections = $this->activeSectionsWithSteps($onboarding);

            $stepsCount = $this->stepsCount($onboarding);
            if ($stepsCount <= 0) {
                continue;
            }

            $progress = $this->progress($publicKey, (int)$onboarding->id);
            $countViewed = min($stepsCount, max(0, (int)$progress->count_viewed));
            $resumeCountViewed = $countViewed >= $stepsCount ? 0 : $countViewed;
            $startSection = $this->sectionForProgress($sections, $resumeCountViewed);
            $result[] = [
                'id' => (int)$onboarding->id,
                'start_url' => $startSection instanceof OnboardingSectionRecord ? (string)$startSection->url : '',
                'start_step' => $startSection instanceof OnboardingSectionRecord
                    ? $this->startStepForSection($sections, $startSection, $resumeCountViewed)
                    : 0,
                'title' => (string)$onboarding->title,
                'auto_start' => (bool)$onboarding->auto_start,
                'count_viewed' => $countViewed,
                'count_unviewed' => max(0, $stepsCount - $countViewed),
            ];
        }

        return $result;
    }

    public function actionTooltip(int $publicKey): array
    {
        return $this->actionOnboardings($publicKey);
    }

    public function actionOnboardingLog(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $onboardingId = (int)Yii::$app->request->get('id_onboarding', 0);
        if ($onboardingId <= 0) {
            return ['ok' => true];
        }

        $progress = $this->progress($publicKey, $onboardingId);
        $stepsCount = $this->stepsCountById($onboardingId);
        $isEnd = (bool)Yii::$app->request->get('is_end', false);
        $isCheck = (bool)Yii::$app->request->get('is_check', false);
        $stepId = (int)Yii::$app->request->get('id_step', 0);
        $stepOrdinal = $stepId > 0 ? $this->stepOrdinal($onboardingId, $stepId) : 0;

        if ($isEnd || $isCheck) {
            $progress->count_viewed = $stepsCount;
            $progress->is_finished = true;
        } elseif ($stepOrdinal > 0) {
            $progress->count_viewed = min($stepsCount, max((int)$progress->count_viewed, $stepOrdinal));
        } else {
            $progress->count_viewed = min($stepsCount, (int)$progress->count_viewed + 1);
        }
        $progress->updated_at = date('Y-m-d H:i:s');
        $progress->save(false);

        return ['ok' => true];
    }

    public function actionSelectorComplete(int $publicKey): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $token = trim((string)Yii::$app->request->post('token', Yii::$app->request->get('token', '')));
        $record = $token === '' ? null : OnboardingSelectorSessionRecord::findOne([
            'token' => $token,
            'public_key' => $publicKey,
        ]);
        if (!$record instanceof OnboardingSelectorSessionRecord) {
            return ['ok' => false, 'message' => 'Сессия выбора элемента не найдена.'];
        }
        if (strtotime((string)$record->expires_at) < time()) {
            return ['ok' => false, 'message' => 'Сессия выбора элемента истекла.'];
        }

        $selector = trim((string)Yii::$app->request->post('selector', ''));
        if ($selector === '') {
            return ['ok' => false, 'message' => 'Пустой селектор.'];
        }

        $record->selector = $selector;
        $record->element_tag = trim((string)Yii::$app->request->post('elementTag', ''));
        $record->element_text = mb_substr(trim((string)Yii::$app->request->post('elementText', '')), 0, 255);
        $record->page_url = trim((string)Yii::$app->request->post('pageUrl', ''));
        $record->selected_at = date('Y-m-d H:i:s');
        $record->save(false);

        return ['ok' => true];
    }

    /**
     * @return OnboardingHintRecord[]
     */
    private function activeHints(int $publicKey): array
    {
        return array_values(array_filter(
            OnboardingHintRecord::find()
                ->where(['public_key' => $publicKey, 'is_active' => true])
                ->orderBy(['id' => SORT_ASC])
                ->all(),
            fn(OnboardingHintRecord $hint): bool => $this->hintAllowed($publicKey, $hint),
        ));
    }

    private function hintAllowed(int $publicKey, OnboardingHintRecord $hint): bool
    {
        $roles = OnboardingHintRoleRecord::find()->where(['hint_id' => $hint->id])->select('role_id')->column();
        if (!$this->rolesAllowed($publicKey, $roles)) {
            return false;
        }

        $urls = OnboardingHintUrlRecord::find()->where(['hint_id' => $hint->id])->all();
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

    private function sectionPayload(OnboardingSectionRecord $section, string $nextUrl = '', int $startStep = 0): array
    {
        $steps = OnboardingStepRecord::find()
            ->where(['section_id' => $section->id, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        $steps = array_values(array_filter(
            $steps,
            fn(OnboardingStepRecord $step): bool => $this->stepSelector($step) !== ''
        ));

        return [
            'id' => (int)$section->id,
            'onboarding_id' => (int)$section->onboarding_id,
            'content' => array_map(fn(OnboardingStepRecord $step): array => $this->stepPayload($step), $steps),
            'next_url' => $nextUrl,
            'start_step' => max(0, min($startStep, count($steps) - 1)),
        ];
    }

    private function requestedStartStep(): ?int
    {
        $raw = Yii::$app->request->get('sitewidget_step', Yii::$app->request->get('step', null));
        if ($raw === null || $raw === '') {
            return null;
        }

        return max(0, (int)$raw);
    }

    private function stepPayload(OnboardingStepRecord $step): array
    {
        $hint = $step->hint_id ? OnboardingHintRecord::findOne($step->hint_id) : null;

        return [
            'id' => (int)$step->id,
            'onbording_part_id' => (int)$step->section_id,
            'text' => trim((string)$step->text) !== '' ? (string)$step->text : ($hint instanceof OnboardingHintRecord ? (string)$hint->content : ''),
            'selector' => $this->stepSelector($step, $hint),
            'position' => (int)($step->position ?? ($hint instanceof OnboardingHintRecord ? $hint->position : 2)),
        ];
    }

    private function stepSelector(OnboardingStepRecord $step, ?OnboardingHintRecord $hint = null): string
    {
        $selector = trim((string)$step->selector);
        if ($selector !== '') {
            return $selector;
        }

        $hint = $hint ?? ($step->hint_id ? OnboardingHintRecord::findOne($step->hint_id) : null);

        return $hint instanceof OnboardingHintRecord ? trim((string)$hint->selector) : '';
    }

    private function hintPayload(OnboardingHintRecord $hint): array
    {
        return [
            'id' => (string)$hint->id,
            'course' => $hint->instruction_id === null ? '' : (string)$hint->instruction_id,
            'content' => (string)$hint->content,
            'position' => (string)$hint->selector,
            'title' => (string)$hint->title,
            'top' => (string)$hint->top_offset,
            'left' => (string)$hint->left_offset,
            'vision' => (string)$hint->vision,
            'type' => (int)$hint->type,
            'view_type' => (string)$hint->view_type,
            'icon_type' => (string)$hint->icon_type,
            'icon_color' => (string)$hint->icon_color,
            'background_color' => (string)$hint->background_color,
            'theme' => (string)$hint->theme,
            'trigger_type' => (string)$hint->trigger_type,
            'hide_after_view' => (bool)$hint->hide_after_view,
            'button_label' => (string)$hint->button_label,
            'button_url' => (string)$hint->button_url,
            'button_instruction_id' => $hint->button_instruction_id === null ? null : (int)$hint->button_instruction_id,
            'type_bind' => (int)(bool)$hint->type_bind,
            'is_leftward' => (int)(bool)$hint->is_leftward,
            'autostart' => (int)$hint->autostart,
        ];
    }

    /**
     * @return OnboardingSectionRecord[]
     */
    private function activeSectionsWithSteps(OnboardingRecord $onboarding): array
    {
        return array_values(array_filter(
            OnboardingSectionRecord::find()
                ->where(['onboarding_id' => $onboarding->id, 'is_active' => true])
                ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
                ->all(),
            fn(OnboardingSectionRecord $section): bool =>
                $this->sectionHasActiveSteps($section),
        ));
    }

    /**
     * @param OnboardingSectionRecord[] $sections
     * @return OnboardingSectionRecord[]
     */
    private function visibleSections(array $sections): array
    {
        return array_values(array_filter(
            $sections,
            fn(OnboardingSectionRecord $section): bool =>
                $section->url === '' || $this->urlMatches((string)$section->url, (bool)$section->include_children, (bool)$section->include_query),
        ));
    }

    /**
     * @param OnboardingSectionRecord[] $sections
     */
    private function nextSectionUrl(array $sections, OnboardingSectionRecord $current): string
    {
        foreach ($sections as $index => $section) {
            if ((int)$section->id !== (int)$current->id) {
                continue;
            }

            $next = $sections[$index + 1] ?? null;

            return $next instanceof OnboardingSectionRecord ? (string)$next->url : '';
        }

        return '';
    }

    /**
     * @param OnboardingSectionRecord[] $sections
     */
    private function startUrlForProgress(array $sections, int $countViewed): string
    {
        $section = $this->sectionForProgress($sections, $countViewed);

        return $section instanceof OnboardingSectionRecord ? (string)$section->url : '';
    }

    /**
     * @param OnboardingSectionRecord[] $sections
     */
    private function sectionForProgress(array $sections, int $countViewed): ?OnboardingSectionRecord
    {
        $seen = 0;
        foreach ($sections as $section) {
            $stepsInSection = count($this->activeSteps($section));
            if ($stepsInSection <= 0) {
                continue;
            }

            if ($countViewed < $seen + $stepsInSection) {
                return $section;
            }

            $seen += $stepsInSection;
        }

        return $sections[0] instanceof OnboardingSectionRecord ? $sections[0] : null;
    }

    /**
     * @param OnboardingSectionRecord[] $sections
     */
    private function startStepForSection(array $sections, OnboardingSectionRecord $current, int $countViewed): int
    {
        $seen = 0;
        foreach ($sections as $section) {
            $stepsInSection = count($this->activeSteps($section));

            if ((int)$section->id === (int)$current->id) {
                return max(0, $countViewed - $seen);
            }

            $seen += $stepsInSection;
        }

        return 0;
    }

    private function sectionHasActiveSteps(OnboardingSectionRecord $section): bool
    {
        return $this->activeSteps($section) !== [];
    }

    /**
     * @return OnboardingStepRecord[]
     */
    private function activeSteps(OnboardingSectionRecord $section): array
    {
        return array_values(array_filter(
            OnboardingStepRecord::find()
                ->where(['section_id' => $section->id, 'is_active' => true])
                ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
                ->all(),
            fn(OnboardingStepRecord $step): bool => $this->stepSelector($step) !== '',
        ));
    }

    private function stepsCount(OnboardingRecord $onboarding): int
    {
        return $this->stepsCountById((int)$onboarding->id);
    }

    private function stepsCountById(int $onboardingId): int
    {
        $sectionIds = OnboardingSectionRecord::find()->where(['onboarding_id' => $onboardingId, 'is_active' => true])->select('id')->column();
        if ($sectionIds === []) {
            return 0;
        }

        $steps = OnboardingStepRecord::find()
            ->where(['section_id' => $sectionIds, 'is_active' => true])
            ->all();
        $count = 0;
        foreach ($steps as $step) {
            if ($step instanceof OnboardingStepRecord && $this->stepSelector($step) !== '') {
                $count++;
            }
        }

        return $count;
    }

    private function stepOrdinal(int $onboardingId, int $stepId): int
    {
        $sectionIds = OnboardingSectionRecord::find()
            ->where(['onboarding_id' => $onboardingId, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->select('id')
            ->column();
        if ($sectionIds === []) {
            return 0;
        }

        $steps = OnboardingStepRecord::find()
            ->where(['section_id' => $sectionIds, 'is_active' => true])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])
            ->all();
        $stepIds = [];
        foreach ($steps as $step) {
            if ($step instanceof OnboardingStepRecord && $this->stepSelector($step) !== '') {
                $stepIds[] = (int)$step->id;
            }
        }
        $index = array_search($stepId, $stepIds, true);

        return $index === false ? 0 : $index + 1;
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

    private function progress(int $publicKey, int $onboardingId): OnboardingProgressRecord
    {
        $visitorKey = $this->visitorKey();
        $progress = OnboardingProgressRecord::findOne([
            'public_key' => $publicKey,
            'onboarding_id' => $onboardingId,
            'visitor_key' => $visitorKey,
        ]);

        if ($progress instanceof OnboardingProgressRecord) {
            return $progress;
        }

        $progress = new OnboardingProgressRecord([
            'public_key' => $publicKey,
            'onboarding_id' => $onboardingId,
            'visitor_key' => $visitorKey,
            'count_viewed' => 0,
            'count_unviewed' => 0,
            'is_finished' => false,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $progress->save(false);

        return $progress;
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
