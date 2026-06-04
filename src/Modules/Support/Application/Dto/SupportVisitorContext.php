<?php

namespace app\Modules\Support\Application\Dto;

final class SupportVisitorContext
{
    public function __construct(
        public readonly ?string $visitorId = null,
        public readonly string $originHost = '',
        public readonly string $remoteAddr = '0.0.0.0',
        public readonly string $pathname = '',
    ) {
    }

    public function resolvedVisitorId(): string
    {
        return $this->visitorId ?: 'anonymous:' . sha1($this->remoteAddr . '|' . $this->originHost);
    }
}
