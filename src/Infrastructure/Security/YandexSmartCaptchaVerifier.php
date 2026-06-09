<?php

namespace app\Infrastructure\Security;

final class YandexSmartCaptchaVerifier
{
    private const VERIFY_URL = 'https://smartcaptcha.yandexcloud.net/validate';

    public function isEnabled(): bool
    {
        return filter_var($_ENV['YANDEX_SMARTCAPTCHA_ENABLED'] ?? false, FILTER_VALIDATE_BOOLEAN)
            && !empty($_ENV['YANDEX_SMARTCAPTCHA_SERVER_KEY'] ?? '');
    }

    public function shouldRenderWidget(): bool
    {
        return $this->isEnabled() && $this->getSiteKey() !== '';
    }

    public function getSiteKey(): string
    {
        return (string)($_ENV['YANDEX_SMARTCAPTCHA_SITE_KEY'] ?? '');
    }

    public function verify(?string $token, ?string $ip): bool
    {
        if (!$this->isEnabled()) {
            return true;
        }

        if ($token === null || $token === '') {
            return false;
        }

        $query = http_build_query([
            'secret' => $_ENV['YANDEX_SMARTCAPTCHA_SERVER_KEY'],
            'token' => $token,
            'ip' => $ip ?? '',
        ]);

        $ch = curl_init(self::VERIFY_URL . '?' . $query);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200 || $response === false) {
            return true;
        }

        $data = json_decode($response, true);

        return is_array($data) && ($data['status'] ?? null) === 'ok';
    }
}
