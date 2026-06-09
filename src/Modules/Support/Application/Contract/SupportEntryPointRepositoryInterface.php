<?php

namespace app\Modules\Support\Application\Contract;

use app\Modules\Support\Domain\SupportEntryPoint;

interface SupportEntryPointRepositoryInterface
{
    /**
     * @return SupportEntryPoint[]
     */
    public function listForClient(int $publicKey, bool $enabledOnly = false): array;

    public function countForClient(int $publicKey): int;

    public function findForClient(int $publicKey, int $id): ?SupportEntryPoint;

    public function save(SupportEntryPoint $entryPoint): bool;

    public function deleteForClient(int $publicKey, int $id): bool;
}
