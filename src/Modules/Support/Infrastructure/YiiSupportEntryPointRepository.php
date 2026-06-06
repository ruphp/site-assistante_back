<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportEntryPointRepositoryInterface;
use app\Modules\Support\Domain\SupportEntryPoint;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportEntryPointRecord;

final class YiiSupportEntryPointRepository implements SupportEntryPointRepositoryInterface
{
    public function listForClient(int $publicKey, bool $enabledOnly = false): array
    {
        $query = SupportEntryPointRecord::find()
            ->where(['public_key' => $publicKey])
            ->orderBy(['sort_order' => SORT_ASC, 'id' => SORT_ASC]);

        if ($enabledOnly) {
            $query->andWhere(['enabled' => 1]);
        }

        return array_map(fn(SupportEntryPointRecord $record): SupportEntryPoint => $this->map($record), $query->all());
    }

    public function countForClient(int $publicKey): int
    {
        return (int)SupportEntryPointRecord::find()
            ->where(['public_key' => $publicKey])
            ->count();
    }

    public function findForClient(int $publicKey, int $id): ?SupportEntryPoint
    {
        $record = SupportEntryPointRecord::find()
            ->where(['id' => $id, 'public_key' => $publicKey])
            ->one();

        return $record ? $this->map($record) : null;
    }

    public function save(SupportEntryPoint $entryPoint): bool
    {
        $record = $entryPoint->id === null
            ? new SupportEntryPointRecord()
            : SupportEntryPointRecord::findOne(['id' => $entryPoint->id, 'public_key' => $entryPoint->publicKey]);

        if ($record === null) {
            return false;
        }

        $record->public_key = $entryPoint->publicKey;
        $record->title = $entryPoint->title;
        $record->description = $entryPoint->description;
        $record->priority = $entryPoint->priority;
        $record->enabled = $entryPoint->enabled ? 1 : 0;
        $record->sort_order = $entryPoint->sortOrder;

        return $record->save(false);
    }

    public function deleteForClient(int $publicKey, int $id): bool
    {
        $record = SupportEntryPointRecord::findOne(['id' => $id, 'public_key' => $publicKey]);

        return $record === null || (bool)$record->delete();
    }

    private function map(SupportEntryPointRecord $record): SupportEntryPoint
    {
        return new SupportEntryPoint(
            id: (int)$record->id,
            publicKey: (int)$record->public_key,
            title: (string)$record->title,
            description: (string)$record->description,
            priority: (int)$record->priority,
            enabled: (bool)$record->enabled,
            sortOrder: (int)$record->sort_order,
        );
    }
}
