<?php

namespace app\Infrastructure\Panel;

use app\Application\Panel\Contract\AssistantDesignStorageInterface;
use RuntimeException;

final class FilesystemAssistantDesignStorage implements AssistantDesignStorageInterface
{
    private const DEFAULT_LOGO_SVG = '<svg width="38" height="38" viewBox="0 0 38 24" fill="#3d8af5" xmlns="http://www.w3.org/2000/svg"><path d="M15.7171 15.4784L14.1244 14.6367L22.5398 0L24.1022 0.871922L15.7171 15.4784Z"></path><path d="M11.5063 3.48226V1.66556L0 7.23656L11.5063 13.1106V11.3544L3.67137 7.29702L11.5063 3.48226Z"></path><path d="M26.4945 3.48223V1.66553L38 7.23653L28.4112 12.1414V19.4473C25.4823 23.0427 19.2231 24.0681 14.3907 22.5266L15.3548 20.847V20.8455C19.1386 21.9701 23.723 21.2687 26.5069 18.7358L26.5076 13.1151L24.0725 14.3607V12.6045L34.3286 7.29698L26.4945 3.48223Z"></path></svg>';

    public function ensureFiles(int $publicKey): void
    {
        $this->ensureDirectory();

        if (!file_exists($this->customCssPath($publicKey))) {
            file_put_contents($this->customCssPath($publicKey), '');
        }

        if (!file_exists($this->logoSvgPath($publicKey))) {
            file_put_contents($this->logoSvgPath($publicKey), self::DEFAULT_LOGO_SVG);
        }
    }

    public function getCustomCss(int $publicKey): string
    {
        return (string)file_get_contents($this->customCssPath($publicKey));
    }

    public function getLogoSvg(int $publicKey): string
    {
        return (string)file_get_contents($this->logoSvgPath($publicKey));
    }

    public function saveCustomCss(int $publicKey, string $customCss): void
    {
        file_put_contents($this->customCssPath($publicKey), $customCss);
    }

    public function saveLogoSvg(int $publicKey, string $logoSvg): void
    {
        $logoSvg = trim($logoSvg);

        if ($logoSvg === '') {
            return;
        }

        if (!$this->isSvg($logoSvg)) {
            throw new RuntimeException('Invalid svg format.');
        }

        file_put_contents($this->logoSvgPath($publicKey), $logoSvg);
    }

    private function ensureDirectory(): void
    {
        if (!is_dir($this->customDir())) {
            mkdir($this->customDir(), 0777, true);
        }
    }

    private function customCssPath(int $publicKey): string
    {
        return $this->customDir() . '/custom_' . $publicKey . '.css';
    }

    private function logoSvgPath(int $publicKey): string
    {
        return $this->customDir() . '/logo' . $publicKey . '.svg';
    }

    private function customDir(): string
    {
        return dirname(__DIR__, 3) . '/web/custom';
    }

    private function isSvg(string $content): bool
    {
        $content = trim($content);

        return str_starts_with($content, '<svg') && str_ends_with($content, '</svg>');
    }
}
