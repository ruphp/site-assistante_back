<?php

namespace app\Application\Panel\Dto;

use app\Presentation\Http\Form\Designe;

final class DesignViewData
{
    public function __construct(
        public readonly mixed $params,
        public readonly Designe $designe,
    ) {
    }

    public function toArray(): array
    {
        return [
            'params' => $this->params,
            'designe' => $this->designe,
        ];
    }
}
