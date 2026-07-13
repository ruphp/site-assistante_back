<?php

namespace app\Modules\Support\Application\Service;

use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportOperatorProjectRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportProjectRecord;

final class SupportOperatorProjectAccessService
{
    /**
     * @return int[]
     */
    public function publicKeysForOperator(int $ownerPublicKey, int $operatorUserId): array
    {
        if ($operatorUserId === $ownerPublicKey) {
            return $this->allProjectPublicKeys($ownerPublicKey);
        }

        $keys = SupportProjectRecord::find()
            ->select('support_projects.public_key')
            ->innerJoin(
                SupportOperatorProjectRecord::tableName(),
                'support_operator_projects.project_id = support_projects.id',
            )
            ->where([
                'support_operator_projects.owner_public_key' => $ownerPublicKey,
                'support_operator_projects.operator_user_id' => $operatorUserId,
                'support_projects.enabled' => 1,
            ])
            ->column();

        return array_values(array_unique(array_map('intval', $keys)));
    }

    /**
     * @return int[]
     */
    public function projectIdsForOperator(int $ownerPublicKey, int $operatorUserId): array
    {
        if ($operatorUserId === $ownerPublicKey) {
            return array_values(array_map('intval', SupportProjectRecord::find()
                ->select('id')
                ->where(['owner_public_key' => $ownerPublicKey, 'enabled' => 1])
                ->column()));
        }

        return array_values(array_unique(array_map('intval', SupportOperatorProjectRecord::find()
            ->select('project_id')
            ->where([
                'owner_public_key' => $ownerPublicKey,
                'operator_user_id' => $operatorUserId,
            ])
            ->column())));
    }

    /**
     * @return int[]
     */
    public function operatorIdsForProject(int $projectPublicKey): array
    {
        $project = SupportProjectRecord::findOne(['public_key' => $projectPublicKey, 'enabled' => 1]);
        if (!$project instanceof SupportProjectRecord) {
            return [$projectPublicKey];
        }

        $ids = SupportOperatorProjectRecord::find()
            ->select('operator_user_id')
            ->where([
                'owner_public_key' => (int)$project->owner_public_key,
                'project_id' => (int)$project->id,
            ])
            ->column();
        $ids[] = (int)$project->owner_public_key;

        return array_values(array_unique(array_map('intval', $ids)));
    }

    /**
     * @param int[] $projectIds
     */
    public function saveAssignments(int $ownerPublicKey, int $operatorUserId, array $projectIds): void
    {
        if ($operatorUserId === $ownerPublicKey) {
            return;
        }

        $allowedProjectIds = SupportProjectRecord::find()
            ->select('id')
            ->where(['owner_public_key' => $ownerPublicKey, 'enabled' => 1])
            ->column();
        $allowedProjectIds = array_map('intval', $allowedProjectIds);
        $projectIds = array_values(array_intersect(
            $allowedProjectIds,
            array_values(array_unique(array_map('intval', $projectIds))),
        ));

        SupportOperatorProjectRecord::deleteAll([
            'owner_public_key' => $ownerPublicKey,
            'operator_user_id' => $operatorUserId,
        ]);

        foreach ($projectIds as $projectId) {
            $record = new SupportOperatorProjectRecord();
            $record->owner_public_key = $ownerPublicKey;
            $record->operator_user_id = $operatorUserId;
            $record->project_id = $projectId;
            $record->save(false);
        }
    }

    /**
     * @return int[]
     */
    private function allProjectPublicKeys(int $ownerPublicKey): array
    {
        $keys = SupportProjectRecord::find()
            ->select('public_key')
            ->where(['owner_public_key' => $ownerPublicKey, 'enabled' => 1])
            ->column();
        $keys = array_values(array_unique(array_map('intval', $keys)));

        return $keys === [] ? [$ownerPublicKey] : $keys;
    }
}
