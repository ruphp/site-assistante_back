<?php

namespace tests\Unit\Presentation\Http\Form;

use app\Presentation\Http\Form\UserJoinForm;
use PHPUnit\Framework\TestCase;

final class UserJoinFormTest extends TestCase
{
    public function testLoadsFirmFromRegistrationData(): void
    {
        $form = new UserJoinForm();

        self::assertTrue($form->load([
            'UserJoinForm' => [
                'firm' => ' Shopsbox ',
            ],
        ]));
        self::assertTrue($form->validate(['firm']));
        self::assertSame('Shopsbox', $form->firm);
    }
}
