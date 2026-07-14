<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientProjectService;
use app\Infrastructure\YiiActiveRecord\Roles;
use app\Modules\Onboarding\Domain\OnboardingPlanLimit;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintRoleRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingHintUrlRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingRoleRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSectionRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingStepRecord;
use app\Modules\Onboarding\Infrastructure\YiiActiveRecord\OnboardingSelectorSessionRecord;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\helpers\Url;
use yii\web\Response;

final class OnboardingController extends ManagerController
{
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }
        if (!$this->isOwnerUser()) {
            Yii::$app->response->redirect('/manager/support/conversations')->send();

            return false;
        }

        return true;
    }

    public function actionIndex(): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $limit = $this->limit($publicKey);
        if (!$limit->enabled) {
            return $this->render('locked', [] + $projects->tabsData($ownerPublicKey, $projectId));
        }

        return $this->render('index', [
            'onboardings' => OnboardingRecord::find()->where(['public_key' => $publicKey])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_DESC])->all(),
            'hintsCount' => OnboardingHintRecord::find()->where(['public_key' => $publicKey])->count(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionCreate(): Response|string
    {
        return $this->editOnboarding(null);
    }

    public function actionUpdate(int $id): Response|string
    {
        return $this->editOnboarding($id);
    }

    public function actionDelete(int $id): Response
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        OnboardingRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);
        Yii::$app->session->setFlash('success', 'Сценарий удален');

        return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
    }

    public function actionSections(int $onboardingId): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $onboarding = OnboardingRecord::findOne(['id' => $onboardingId, 'public_key' => $publicKey]);
        if (!$onboarding instanceof OnboardingRecord) {
            return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('OnboardingSection', []);
            $section = empty($data['id'])
                ? new OnboardingSectionRecord(['onboarding_id' => $onboarding->id])
                : OnboardingSectionRecord::findOne(['id' => (int)$data['id'], 'onboarding_id' => $onboarding->id]);
            if ($section instanceof OnboardingSectionRecord) {
                $section->title = trim((string)($data['title'] ?? 'Раздел'));
                $section->url = trim((string)($data['url'] ?? ''));
                $section->include_children = (bool)($data['include_children'] ?? false);
                $section->include_query = (bool)($data['include_query'] ?? false);
                $section->sort_order = (int)($data['sort_order'] ?? 100);
                $section->is_active = (bool)($data['is_active'] ?? false);
                $section->save();
            }

            return $this->redirect($this->projectUrl('/manager/onboarding/sections', $projectId, ['onboardingId' => $onboarding->id]));
        }

        return $this->render('sections', [
            'onboarding' => $onboarding,
            'sections' => OnboardingSectionRecord::find()->where(['onboarding_id' => $onboarding->id])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->all(),
            'editSection' => $this->editableSection($onboarding),
            'urlBindingsEnabled' => $this->limit($publicKey)->urlBindingsEnabled,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionSectionDelete(int $id): Response
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $section = OnboardingSectionRecord::findOne($id);
        if ($section instanceof OnboardingSectionRecord) {
            $onboardingId = (int)$section->onboarding_id;
            $section->delete();

            return $this->redirect($this->projectUrl('/manager/onboarding/sections', $projectId, ['onboardingId' => $onboardingId]));
        }

        return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
    }

    public function actionSteps(int $sectionId): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $section = OnboardingSectionRecord::findOne($sectionId);
        $onboarding = $section instanceof OnboardingSectionRecord
            ? OnboardingRecord::findOne(['id' => $section->onboarding_id, 'public_key' => $publicKey])
            : null;
        if (!$section instanceof OnboardingSectionRecord || !$onboarding instanceof OnboardingRecord) {
            return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('OnboardingStep', []);
            $step = empty($data['id'])
                ? new OnboardingStepRecord(['section_id' => $section->id])
                : OnboardingStepRecord::findOne(['id' => (int)$data['id'], 'section_id' => $section->id]);
            if ($step instanceof OnboardingStepRecord) {
                $step->hint_id = empty($data['hint_id']) ? null : (int)$data['hint_id'];
                $step->text = (string)($data['text'] ?? '');
                $step->selector = trim((string)($data['selector'] ?? ''));
                $step->position = (int)($data['position'] ?? 2);
                $step->sort_order = (int)($data['sort_order'] ?? 100);
                $step->is_active = (bool)($data['is_active'] ?? false);
                $step->save();
            }

            return $this->redirect($this->projectUrl('/manager/onboarding/steps', $projectId, ['sectionId' => $section->id]));
        }

        return $this->render('steps', [
            'onboarding' => $onboarding,
            'section' => $section,
            'steps' => OnboardingStepRecord::find()->where(['section_id' => $section->id])->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC])->all(),
            'editStep' => $this->editableStep($section),
            'hints' => OnboardingHintRecord::find()->where(['public_key' => $publicKey])->orderBy(['title' => SORT_ASC])->all(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionStepDelete(int $id): Response
    {
        [$projects, $ownerPublicKey, $projectId] = $this->projectContext();
        $step = OnboardingStepRecord::findOne($id);
        if ($step instanceof OnboardingStepRecord) {
            $sectionId = (int)$step->section_id;
            $step->delete();

            return $this->redirect($this->projectUrl('/manager/onboarding/steps', $projectId, ['sectionId' => $sectionId]));
        }

        return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
    }

    public function actionHints(): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();

        return $this->render('hints', [
            'hints' => OnboardingHintRecord::find()->where(['public_key' => $publicKey])->orderBy(['id' => SORT_DESC])->all(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionHintCreate(): Response|string
    {
        return $this->editHint(null);
    }

    public function actionHintUpdate(int $id): Response|string
    {
        return $this->editHint($id);
    }

    public function actionHintDelete(int $id): Response
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        OnboardingHintRecord::deleteAll(['id' => $id, 'public_key' => $publicKey]);

        return $this->redirect($this->projectUrl('/manager/onboarding/hints', $projectId));
    }

    public function actionSelectorSession(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $activeProject = $projects->activeProject($ownerPublicKey, $projectId);
        $url = trim((string)Yii::$app->request->post('url', ''));
        if ($url === '') {
            $url = $activeProject->domain;
        }
        $url = $this->normalizeSiteUrl($url);
        if ($url === '') {
            return ['ok' => false, 'message' => 'Укажите URL страницы, на которой нужно выбрать элемент.'];
        }

        $record = new OnboardingSelectorSessionRecord([
            'token' => Yii::$app->security->generateRandomString(48),
            'owner_public_key' => $ownerPublicKey,
            'public_key' => $publicKey,
            'target' => trim((string)Yii::$app->request->post('target', 'hint')),
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => date('Y-m-d H:i:s', time() + 1800),
        ]);
        $record->save(false);

        return [
            'ok' => true,
            'token' => $record->token,
            'url' => $url . (strpos($url, '#') !== false ? '&' : '#') . 'sitewidget-selector=' . rawurlencode($record->token),
        ];
    }

    public function actionSelectorStatus(string $token): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $record = OnboardingSelectorSessionRecord::findOne([
            'token' => $token,
            'owner_public_key' => $ownerPublicKey,
            'public_key' => $publicKey,
        ]);
        if (!$record instanceof OnboardingSelectorSessionRecord) {
            return ['ok' => false, 'message' => 'Сессия выбора не найдена.'];
        }

        return [
            'ok' => true,
            'selected' => trim((string)$record->selector) !== '',
            'selector' => (string)$record->selector,
            'element' => trim((string)$record->element_text) !== '' ? (string)$record->element_text : (string)$record->element_tag,
            'pageUrl' => (string)$record->page_url,
        ];
    }

    private function editOnboarding(?int $id): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $onboarding = $id === null
            ? new OnboardingRecord(['public_key' => $publicKey, 'is_active' => true, 'sort_order' => 100])
            : OnboardingRecord::findOne(['id' => $id, 'public_key' => $publicKey]);
        if (!$onboarding instanceof OnboardingRecord) {
            return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('Onboarding', []);
            $onboarding->public_key = $publicKey;
            $onboarding->title = trim((string)($data['title'] ?? ''));
            $onboarding->timeout = (int)($data['timeout'] ?? 0);
            $onboarding->type = (int)($data['type'] ?? 0);
            $onboarding->is_blur = (bool)($data['is_blur'] ?? false);
            $onboarding->is_active = (bool)($data['is_active'] ?? false);
            $onboarding->sort_order = (int)($data['sort_order'] ?? 100);
            if ($onboarding->save()) {
                $this->saveRoles((int)$onboarding->id, Yii::$app->request->post('role_ids', []), true);
            }

            return $this->redirect($this->projectUrl('/manager/onboarding', $projectId));
        }

        return $this->render('form', [
            'onboarding' => $onboarding,
            'roles' => Roles::find()->where(['public_key' => $publicKey])->orderBy(['name' => SORT_ASC])->all(),
            'selectedRoleIds' => $onboarding->isNewRecord ? [] : OnboardingRoleRecord::find()->where(['onboarding_id' => $onboarding->id])->select('role_id')->column(),
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    private function editHint(?int $id): Response|string
    {
        [$projects, $ownerPublicKey, $projectId, $publicKey] = $this->projectContext();
        $hint = $id === null
            ? new OnboardingHintRecord(['public_key' => $publicKey, 'is_active' => true, 'standalone_enabled' => false, 'position' => 2])
            : OnboardingHintRecord::findOne(['id' => $id, 'public_key' => $publicKey]);
        if (!$hint instanceof OnboardingHintRecord) {
            return $this->redirect($this->projectUrl('/manager/onboarding/hints', $projectId));
        }

        if (Yii::$app->request->isPost) {
            $data = Yii::$app->request->post('OnboardingHint', []);
            $hint->public_key = $publicKey;
            $hint->title = trim((string)($data['title'] ?? ''));
            $hint->content = (string)($data['content'] ?? '');
            $hint->selector = trim((string)($data['selector'] ?? ''));
            $hint->position = (int)($data['position'] ?? 2);
            $hint->type_bind = (bool)($data['type_bind'] ?? false);
            $hint->standalone_enabled = (bool)($data['standalone_enabled'] ?? false);
            $hint->is_active = (bool)($data['is_active'] ?? false);
            $hint->left_offset = (int)($data['left_offset'] ?? 0);
            $hint->top_offset = (int)($data['top_offset'] ?? 0);
            if ($hint->save()) {
                $this->saveRoles((int)$hint->id, Yii::$app->request->post('role_ids', []), false);
                $this->saveHintUrls($publicKey, $hint, $data);
            }

            return $this->redirect($this->projectUrl('/manager/onboarding/hints', $projectId));
        }

        return $this->render('hint-form', [
            'hint' => $hint,
            'roles' => Roles::find()->where(['public_key' => $publicKey])->orderBy(['name' => SORT_ASC])->all(),
            'selectedRoleIds' => $hint->isNewRecord ? [] : OnboardingHintRoleRecord::find()->where(['hint_id' => $hint->id])->select('role_id')->column(),
            'hintUrls' => $hint->isNewRecord ? [] : OnboardingHintUrlRecord::find()->where(['hint_id' => $hint->id])->all(),
            'urlBindingsEnabled' => $this->limit($publicKey)->urlBindingsEnabled,
        ] + $projects->tabsData($ownerPublicKey, $projectId));
    }

    private function editableSection(OnboardingRecord $onboarding): OnboardingSectionRecord
    {
        $editId = (int)Yii::$app->request->get('editId', 0);
        if ($editId > 0) {
            $section = OnboardingSectionRecord::findOne(['id' => $editId, 'onboarding_id' => $onboarding->id]);
            if ($section instanceof OnboardingSectionRecord) {
                return $section;
            }
        }

        return new OnboardingSectionRecord([
            'onboarding_id' => $onboarding->id,
            'title' => '',
            'url' => '',
            'sort_order' => 100,
            'is_active' => true,
        ]);
    }

    private function editableStep(OnboardingSectionRecord $section): OnboardingStepRecord
    {
        $editId = (int)Yii::$app->request->get('editId', 0);
        if ($editId > 0) {
            $step = OnboardingStepRecord::findOne(['id' => $editId, 'section_id' => $section->id]);
            if ($step instanceof OnboardingStepRecord) {
                return $step;
            }
        }

        return new OnboardingStepRecord([
            'section_id' => $section->id,
            'position' => 2,
            'sort_order' => 100,
            'is_active' => true,
        ]);
    }

    private function saveRoles(int $sourceId, array $roleIds, bool $onboarding): void
    {
        $class = $onboarding ? OnboardingRoleRecord::class : OnboardingHintRoleRecord::class;
        $field = $onboarding ? 'onboarding_id' : 'hint_id';
        $class::deleteAll([$field => $sourceId]);
        foreach (array_unique(array_map('intval', $roleIds)) as $roleId) {
            if ($roleId > 0) {
                (new $class([$field => $sourceId, 'role_id' => $roleId]))->save(false);
            }
        }
    }

    private function saveHintUrls(int $publicKey, OnboardingHintRecord $hint, array $data): void
    {
        if (!$this->limit($publicKey)->urlBindingsEnabled) {
            return;
        }

        OnboardingHintUrlRecord::deleteAll(['hint_id' => $hint->id]);
        foreach (preg_split('/\r\n|\r|\n/', (string)($data['urls'] ?? '')) ?: [] as $rawUrl) {
            $rawUrl = trim($rawUrl);
            if ($rawUrl === '') {
                continue;
            }
            [$url, $children, $query] = array_pad(array_map('trim', explode('|', $rawUrl)), 3, '');
            (new OnboardingHintUrlRecord([
                'hint_id' => $hint->id,
                'public_key' => $publicKey,
                'url' => $url,
                'include_children' => in_array($children, ['1', 'yes', 'true', 'children'], true),
                'include_query' => in_array($query, ['1', 'yes', 'true', 'query'], true),
            ]))->save(false);
        }
    }

    private function limit(int $publicKey): OnboardingPlanLimit
    {
        return OnboardingPlanLimit::forPlan(Yii::$container->get(SupportSettingsRepositoryInterface::class)->getForClient($publicKey)->plan);
    }

    private function isOwnerUser(): bool
    {
        return !Yii::$app->user->isGuest && (int)Yii::$app->user->id === (int)Yii::$app->user->identity->getPublicKey();
    }

    private function projectContext(): array
    {
        $projects = Yii::$container->get(ClientProjectService::class);
        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $projects->publicKeyForProject($ownerPublicKey, $projectId);

        return [$projects, $ownerPublicKey, $projectId, $publicKey];
    }

    private function projectUrl(string $path, ?int $projectId, array $extra = []): string
    {
        $params = [$path] + $extra;
        if ($projectId !== null) {
            $params['projectId'] = $projectId;
        }

        return Url::to($params);
    }

    private function normalizeSiteUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }
        if (!preg_match('~^https?://~i', $url)) {
            $url = 'https://' . $url;
        }

        return $url;
    }
}
