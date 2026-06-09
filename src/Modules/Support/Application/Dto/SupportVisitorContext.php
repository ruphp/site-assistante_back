<?php

namespace app\Modules\Support\Application\Dto;

final class SupportVisitorContext
{
    public function __construct(
        public readonly ?string $visitorId = null,
        public readonly ?string $visitorEmail = null,
        public readonly string $originHost = '',
        public readonly string $remoteAddr = '0.0.0.0',
        public readonly string $pathname = '',
        public readonly string $pageUrl = '',
    ) {
    }

    public function resolvedVisitorId(): string
    {
        $visitorId = trim((string)$this->visitorId);
        if ($visitorId !== '') {
            return $visitorId;
        }

        $email = strtolower(trim((string)$this->visitorEmail));
        if ($email !== '') {
            return 'email:' . $email;
        }

        return 'anonymous:' . sha1($this->remoteAddr . '|' . $this->originHost);
    }
}
