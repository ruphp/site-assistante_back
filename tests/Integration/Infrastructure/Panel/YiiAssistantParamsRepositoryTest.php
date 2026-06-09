<?php

namespace tests\Integration\Infrastructure\Panel;

use app\Infrastructure\Panel\YiiAssistantParamsRepository;
use app\Infrastructure\YiiActiveRecord\Params;
use tests\Integration\Support\YiiIntegrationTestCase;

final class YiiAssistantParamsRepositoryTest extends YiiIntegrationTestCase
{
    public function testSaveFromPostKeepsCurrentClientPublicKey(): void
    {
        $currentPublicKey = 990201;
        $foreignPublicKey = 990202;

        $this->createClient($currentPublicKey);
        $this->createClient($foreignPublicKey);

        $repository = new YiiAssistantParamsRepository();
        $params = $repository->findOrCreateForClient($currentPublicKey);

        self::assertTrue($repository->saveFromPost($params, [
            'Params' => [
                'public_key' => $foreignPublicKey,
                'domain' => 'https://client.example',
                'run' => 1,
                'tab_tickets' => 0,
                'tab_tp_contacts' => 0,
                'leftbutton' => 1,
                'timeout' => 5,
                'is_uuid' => 0,
            ],
        ], $currentPublicKey));

        $saved = Params::findOne(['public_key' => $currentPublicKey]);

        self::assertNotNull($saved);
        self::assertSame($currentPublicKey, (int)$saved->public_key);
        self::assertSame('https://client.example', $saved->domain);
        self::assertNull(Params::findOne(['public_key' => $foreignPublicKey]));
    }
}
