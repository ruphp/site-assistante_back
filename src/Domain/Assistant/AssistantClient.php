<?php

namespace app\Domain\Assistant;

final class AssistantClient
{
    public function __construct(
        public readonly int $publicKey,
        public readonly array $params,
    ) {
    }

    public function enabledModules(): array
    {
        return $this->params['widget_modules'] ?? [];
    }

    public function allowedHosts(): array
    {
        $hosts = [];

        foreach (explode(',', (string)($this->params['domain'] ?? '')) as $domain) {
            $host = $this->hostFromDomain(trim($domain));

            if ($host !== '') {
                $hosts[] = $host;
            }
        }

        return $hosts;
    }

    public function allowsHost(string $host): bool
    {
        if ($host === '') {
            return false;
        }

        return in_array($host, $this->allowedHosts(), true);
    }

    private function hostFromDomain(string $domain): string
    {
        if ($domain === '') {
            return '';
        }

        $host = parse_url($domain, PHP_URL_HOST);

        if ($host !== null) {
            return $host;
        }

        $host = parse_url('https://' . $domain, PHP_URL_HOST);

        return $host ?: '';
    }
}
