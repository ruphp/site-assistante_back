<?php

namespace app\Application\Panel;

use app\Application\Panel\Dto\ClientProjectView;
use app\Infrastructure\YiiActiveRecord\Params;
use app\Infrastructure\YiiActiveRecord\Users;
use app\Modules\Support\Application\Contract\SupportSettingsRepositoryInterface;
use app\Modules\Support\Domain\SupportSettings;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;
use Yii;

final class ClientProjectService
{
    public function __construct(
        private readonly SupportSettingsRepositoryInterface $supportSettings,
    ) {
    }

    /**
     * @return ClientProjectView[]
     */
    public function projectsForOwner(int $ownerPublicKey): array
    {
        $this->ensureDefaultProject($ownerPublicKey);

        return array_map(
            fn(SupportProjectRecord $record): ClientProjectView => $this->map($record),
            SupportProjectRecord::find()
                ->where([
                    'owner_public_key' => $ownerPublicKey,
                    'enabled' => 1,
                ])
                ->orderBy(['is_default' => SORT_DESC, 'id' => SORT_ASC])
                ->all(),
        );
    }

    public function activeProject(int $ownerPublicKey, ?int $projectId = null): ClientProjectView
    {
        $projects = $this->projectsForOwner($ownerPublicKey);

        foreach ($projects as $project) {
            if ($projectId !== null && $project->id === $projectId) {
                return $project;
            }
        }

        return $projects[0];
    }

    public function publicKeyForProject(int $ownerPublicKey, ?int $projectId = null): int
    {
        return $this->activeProject($ownerPublicKey, $projectId)->publicKey;
    }

    public function ensureOwnerProject(int $ownerPublicKey, string $name = '', string $domain = ''): void
    {
        $this->ensureDefaultProject($ownerPublicKey, $name, $domain);
    }

    public function create(int $ownerPublicKey, string $name, string $domain = ''): bool
    {
        $this->ensureDefaultProject($ownerPublicKey);
        $projectName = trim($name) !== '' ? trim($name) : 'Новый проект';
        $projectDomain = $this->firstDomain($domain);
        $projectUser = new Users();
        $projectUser->name = mb_substr(preg_replace('/[^a-zA-Z0-9_-]+/', '-', $projectName) ?: 'project', 0, 64);
        $projectUser->email = 'project-' . Yii::$app->security->generateRandomString(12) . '@sitewidget.local';
        $projectUser->firm = $projectName;
        $projectUser->public_key = null;
        $projectUser->setPassword(Yii::$app->security->generateRandomString(32));
        $projectUser->status = Users::STATUS_ACTIVE;

        if (!$projectUser->save()) {
            return false;
        }

        $projectUser->refresh();
        $projectPublicKey = (int)$projectUser->public_key;
        if ($projectPublicKey <= 0) {
            return false;
        }

        $params = new Params();
        $params->public_key = $projectPublicKey;
        $params->domain = $projectDomain !== '' ? $projectDomain : '';
        $params->save(false);

        $this->supportSettings->save(new SupportSettings(
            publicKey: $projectPublicKey,
            plan: $this->supportSettings->getForClient($ownerPublicKey)->plan,
        ));

        $record = new SupportProjectRecord();
        $record->owner_public_key = $ownerPublicKey;
        $record->public_key = $projectPublicKey;
        $record->name = $projectName;
        $record->domain = $projectDomain !== '' ? $projectDomain : null;
        $record->enabled = 1;
        $record->is_default = 0;

        return $record->save();
    }

    /**
     * @return array{projects: ClientProjectView[], activeProject: ClientProjectView}
     */
    public function tabsData(int $ownerPublicKey, ?int $projectId = null): array
    {
        $projects = $this->projectsForOwner($ownerPublicKey);
        $activeProject = $this->activeProject($ownerPublicKey, $projectId);

        return [
            'projects' => $projects,
            'activeProject' => $activeProject,
        ];
    }

    private function ensureDefaultProject(int $ownerPublicKey, string $name = '', string $domain = ''): void
    {
        $record = SupportProjectRecord::find()
            ->where([
                'owner_public_key' => $ownerPublicKey,
                'is_default' => 1,
            ])
            ->one();

        if ($record instanceof SupportProjectRecord) {
            $changed = false;
            if (trim($name) !== '' && (string)$record->name === 'Основной сайт') {
                $record->name = trim($name);
                $changed = true;
            }
            $firstDomain = $this->firstDomain($domain);
            if ($firstDomain !== '' && ($record->domain === null || (string)$record->domain === '')) {
                $record->domain = $firstDomain;
                $changed = true;
            }
            if ($changed) {
                $record->save(false);
            }
            return;
        }

        $record = new SupportProjectRecord();
        $record->owner_public_key = $ownerPublicKey;
        $record->public_key = $ownerPublicKey;
        $record->name = trim($name) !== '' ? trim($name) : 'Основной сайт';
        $firstDomain = $this->firstDomain($domain);
        $record->domain = $firstDomain !== '' ? $firstDomain : null;
        $record->enabled = 1;
        $record->is_default = 1;
        $record->save(false);
    }

    private function map(SupportProjectRecord $record): ClientProjectView
    {
        return new ClientProjectView(
            id: (int)$record->id,
            ownerPublicKey: (int)$record->owner_public_key,
            publicKey: (int)$record->public_key,
            name: (string)$record->name,
            domain: $record->domain === null ? '' : (string)$record->domain,
            enabled: (bool)$record->enabled,
            isDefault: (bool)$record->is_default,
        );
    }

    private function firstDomain(string $value): string
    {
        $parts = preg_split('/[\s,;]+/', trim($value)) ?: [];

        return trim((string)($parts[0] ?? ''));
    }
}
