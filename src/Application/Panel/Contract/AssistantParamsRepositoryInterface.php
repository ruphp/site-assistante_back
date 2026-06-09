<?php

namespace app\Application\Panel\Contract;

interface AssistantParamsRepositoryInterface
{
    public function findForClient(int $publicKey): mixed;

    public function findOrCreateForClient(int $publicKey): mixed;

    public function saveFromPost(mixed $params, array $post, int $publicKey): bool;
}
