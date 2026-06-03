<?php

namespace app\Application\Panel\Contract;

interface AssistantDesignStorageInterface
{
    public function ensureFiles(int $publicKey): void;

    public function getCustomCss(int $publicKey): string;

    public function getLogoSvg(int $publicKey): string;

    public function saveCustomCss(int $publicKey, string $customCss): void;

    public function saveLogoSvg(int $publicKey, string $logoSvg): void;
}
