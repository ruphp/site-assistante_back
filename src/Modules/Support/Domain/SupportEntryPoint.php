<?php

namespace app\Modules\Support\Domain;

final class SupportEntryPoint
{
    public const RESPONSE_ANSWER = 'answer';
    public const RESPONSE_QUESTION = 'question';

    public function __construct(
        public readonly ?int $id,
        public readonly int $publicKey,
        public readonly string $title,
        public readonly string $description = '',
        public readonly string $responseType = self::RESPONSE_ANSWER,
        public readonly int $priority = 1,
        public readonly bool $enabled = true,
        public readonly int $sortOrder = 100,
    ) {
    }

    public function normalizedPriority(int $maxPriority = 5): int
    {
        return max(1, min($maxPriority, $this->priority));
    }

    public static function normalizeResponseType(string $responseType): string
    {
        return in_array($responseType, [self::RESPONSE_ANSWER, self::RESPONSE_QUESTION], true)
            ? $responseType
            : self::RESPONSE_ANSWER;
    }
}
