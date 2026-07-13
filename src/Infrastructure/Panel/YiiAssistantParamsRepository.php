<?php

namespace app\Infrastructure\Panel;

use app\Application\Panel\Contract\AssistantParamsRepositoryInterface;
use app\Infrastructure\YiiActiveRecord\Params;

final class YiiAssistantParamsRepository implements AssistantParamsRepositoryInterface
{
    public function findForClient(int $publicKey): mixed
    {
        return Params::find()->where(['public_key' => $publicKey])->one();
    }

    public function findOrCreateForClient(int $publicKey): mixed
    {
        $params = $this->findForClient($publicKey);

        if ($params !== null) {
            return $params;
        }

        $params = new Params();
        $params->public_key = $publicKey;

        return $params;
    }

    public function saveFromPost(mixed $params, array $post, int $publicKey): bool
    {
        if (isset($post['Params']['domain'])) {
            $post['Params']['domain'] = $this->firstDomain((string)$post['Params']['domain']);
        }

        if (!$params->load($post)) {
            return false;
        }

        $params->public_key = $publicKey;

        return $params->save();
    }

    private function firstDomain(string $value): string
    {
        $parts = preg_split('/[\s,;]+/', trim($value)) ?: [];

        return trim((string)($parts[0] ?? ''));
    }
}
