<?php

declare(strict_types=1);

namespace SiteWidget\Sms;

final class SiteWidgetSmsClient
{
    public function __construct(
        private readonly string $apiToken,
        private readonly string $baseUrl = 'https://sitewidget.ru',
        private readonly int $timeoutSeconds = 5,
    ) {
    }

    public function createTask(
        string $requestId,
        string $phone,
        string $text,
        ?\DateTimeInterface $expiresAt = null,
    ): array {
        return $this->request('POST', '/api/sms/v1/tasks', [
            'requestId' => $requestId,
            'phone' => $phone,
            'text' => $text,
            'expiresAt' => ($expiresAt ?? new \DateTimeImmutable('+5 minutes'))->format(DATE_ATOM),
        ]);
    }

    public function status(string $requestId): array
    {
        return $this->request('GET', '/api/sms/v1/tasks/' . rawurlencode($requestId));
    }

    public static function verifyCallback(string $rawBody, string $signature, string $callbackSecret): bool
    {
        return $signature !== '' && hash_equals(
            hash_hmac('sha256', $rawBody, $callbackSecret),
            $signature,
        );
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $curl = curl_init(rtrim($this->baseUrl, '/') . $path);
        if ($curl === false) {
            throw new \RuntimeException('Не удалось создать HTTP-запрос');
        }

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->apiToken,
        ];
        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $this->timeoutSeconds,
            CURLOPT_TIMEOUT => $this->timeoutSeconds,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ];
        if ($payload !== null) {
            $encoded = json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $options[CURLOPT_POSTFIELDS] = $encoded;
            $options[CURLOPT_HTTPHEADER][] = 'Content-Type: application/json';
        }

        curl_setopt_array($curl, $options);
        $body = curl_exec($curl);
        $status = (int)curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
        $error = curl_error($curl);
        curl_close($curl);

        if ($body === false) {
            throw new \RuntimeException('SiteWidget недоступен: ' . $error);
        }

        $data = json_decode($body, true);
        if (!is_array($data)) {
            throw new \RuntimeException('SiteWidget вернул некорректный ответ');
        }
        if ($status < 200 || $status >= 300) {
            throw new \RuntimeException((string)($data['message'] ?? ('SiteWidget HTTP ' . $status)), $status);
        }

        return $data;
    }
}
