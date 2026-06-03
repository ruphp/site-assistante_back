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
        if (!$params->load($post)) {
            return false;
        }

        $params->public_key = $publicKey;

        return $params->save();
    }
}
