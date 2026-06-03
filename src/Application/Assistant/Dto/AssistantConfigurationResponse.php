<?php

namespace app\Application\Assistant\Dto;

final class AssistantConfigurationResponse
{
    public function __construct(
        public readonly array $error,
        public readonly string $position,
        public readonly mixed $run,
        public readonly mixed $theme,
        public readonly array $domain,
        public readonly int $typeTickets,
        public readonly string $textContacts,
        public readonly mixed $zeroLogDelay,
        public readonly mixed $urlSmguideTp,
        public readonly array $modules,
    ) {
    }

    public function withRedisError(): self
    {
        $error = $this->error;
        $error['redis'] = 'no connect';

        return new self(
            $error,
            $this->position,
            $this->run,
            $this->theme,
            $this->domain,
            $this->typeTickets,
            $this->textContacts,
            $this->zeroLogDelay,
            $this->urlSmguideTp,
            $this->modules,
        );
    }

    public function toArray(): array
    {
        return [
            'error' => $this->error,
            'position' => $this->position,
            'run' => $this->run,
            'theme' => $this->theme,
            'domain' => $this->domain,
            'type_tickets' => $this->typeTickets,
            'text_contacts' => $this->textContacts,
            'zero_log_delay' => $this->zeroLogDelay,
            'url_smguide_tp' => $this->urlSmguideTp,
            'modules' => $this->modules,
        ];
    }
}
