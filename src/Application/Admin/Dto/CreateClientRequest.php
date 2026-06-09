<?php

namespace app\Application\Admin\Dto;

use app\Presentation\Http\Form\UserJoinForm;

final class CreateClientRequest
{
    public function __construct(
        public readonly string $name,
        public readonly string $email,
        public readonly string $firm,
        public readonly string $password,
    ) {
    }

    public static function fromJoinForm(UserJoinForm $form): self
    {
        return new self(
            (string)$form->name,
            mb_strtolower((string)$form->email),
            (string)$form->firm,
            (string)$form->password,
        );
    }
}
