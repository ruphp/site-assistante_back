<?php

namespace app\Modules\Support\Application\Contract;

interface SupportManagerRecipientRepositoryInterface
{
    public function listForClient(int $publicKey): array;
}
