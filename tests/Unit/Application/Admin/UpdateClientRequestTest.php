<?php

namespace tests\Unit\Application\Admin;

use app\Application\Admin\Dto\UpdateClientRequest;
use PHPUnit\Framework\TestCase;

final class UpdateClientRequestTest extends TestCase
{
    public function testNormalizesBigdataAccessWhenChatbotsIsDisabled(): void
    {
        $request = UpdateClientRequest::fromPost(10, [
            'Users' => [
                'modules' => [
                    'chatbots' => 0,
                    'bigdata' => 1,
                ],
            ],
        ]);

        self::assertSame(0, $request->modules['bigdata']);
    }

    public function testKeepsBigdataAccessWhenChatbotsIsEnabled(): void
    {
        $request = UpdateClientRequest::fromPost(10, [
            'Users' => [
                'modules' => [
                    'chatbots' => 1,
                    'bigdata' => 1,
                ],
            ],
        ]);

        self::assertSame(1, $request->modules['bigdata']);
    }
}
