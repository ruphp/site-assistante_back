<?php

namespace app\Modules\Support\Infrastructure;

use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use Yii;
use yii\httpclient\Client;

final class FirebaseHttpV1PushNotificationSender implements SupportPushNotificationSenderInterface
{
    private const SCOPE = 'https://www.googleapis.com/auth/firebase.messaging';

    public function isConfigured(): bool
    {
        return $this->env('FIREBASE_PROJECT_ID') !== ''
            && $this->env('FIREBASE_CLIENT_EMAIL') !== ''
            && $this->env('FIREBASE_PRIVATE_KEY') !== '';
    }

    public function sendToToken(string $token, string $title, string $body, array $data = []): void
    {
        $token = trim($token);
        $title = trim($title);
        $body = trim($body);

        if ($token === '' || $title === '' || $body === '') {
            return;
        }

        if (!$this->isConfigured()) {
            return;
        }

        $projectId = $this->env('FIREBASE_PROJECT_ID');
        $clientEmail = $this->env('FIREBASE_CLIENT_EMAIL');
        $privateKey = $this->env('FIREBASE_PRIVATE_KEY');

        $accessToken = $this->accessToken($clientEmail, $privateKey);
        if ($accessToken === '') {
            return;
        }

        $payload = [
            'message' => [
                'token' => $token,
                'android' => [
                    'priority' => 'HIGH',
                ],
                'data' => array_map(static fn($value): string => (string)$value, array_merge([
                    'title' => $title,
                    'body' => $body,
                ], $data)),
            ],
        ];

        try {
            $response = (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl(sprintf('https://fcm.googleapis.com/v1/projects/%s/messages:send', $projectId))
                ->addHeaders([
                    'Authorization' => 'Bearer ' . $accessToken,
                    'Content-Type' => 'application/json',
                ])
                ->setFormat(Client::FORMAT_JSON)
                ->setData($payload)
                ->send();

            if (!$response->isOk) {
                Yii::warning([
                    'status' => $response->statusCode,
                    'content' => (string)$response->content,
                    'token_tail' => substr($token, -12),
                ], 'support-push');
            }
        } catch (\Throwable $e) {
            Yii::warning($e->getMessage(), 'support-push');
        }
    }

    private function accessToken(string $clientEmail, string $privateKey): string
    {
        $cacheKey = 'firebase:fcm:access-token';
        $cached = Yii::$app->cache->get($cacheKey);
        if (is_array($cached) && !empty($cached['token']) && !empty($cached['expires_at']) && (int)$cached['expires_at'] > time() + 60) {
            return (string)$cached['token'];
        }

        $assertion = $this->buildJwt($clientEmail, $privateKey);
        if ($assertion === '') {
            return '';
        }

        try {
            $response = (new Client())->createRequest()
                ->setMethod('POST')
                ->setUrl('https://oauth2.googleapis.com/token')
                ->setFormat(Client::FORMAT_URLENCODED)
                ->setData([
                    'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                    'assertion' => $assertion,
                ])
                ->send();

            if (!$response->isOk) {
                Yii::warning([
                    'status' => $response->statusCode,
                    'content' => (string)$response->content,
                ], 'support-push-token');
                return '';
            }

            $data = json_decode((string)$response->content, true);
            $token = (string)($data['access_token'] ?? '');
            $expiresIn = (int)($data['expires_in'] ?? 0);
            if ($token === '' || $expiresIn <= 0) {
                return '';
            }

            Yii::$app->cache->set($cacheKey, [
                'token' => $token,
                'expires_at' => time() + $expiresIn,
            ], max(60, $expiresIn - 60));

            return $token;
        } catch (\Throwable $e) {
            Yii::warning($e->getMessage(), 'support-push');
            return '';
        }
    }

    private function buildJwt(string $clientEmail, string $privateKey): string
    {
        $header = $this->base64UrlEncode(json_encode([
            'alg' => 'RS256',
            'typ' => 'JWT',
        ], JSON_UNESCAPED_SLASHES));

        $now = time();
        $claims = $this->base64UrlEncode(json_encode([
            'iss' => $clientEmail,
            'scope' => self::SCOPE,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
        ], JSON_UNESCAPED_SLASHES));

        $data = $header . '.' . $claims;
        $signature = '';
        $normalizedKey = str_replace(['\\n', "\r\n", "\r"], "\n", trim($privateKey));
        $resource = openssl_pkey_get_private($normalizedKey);
        if ($resource === false) {
            return '';
        }

        openssl_sign($data, $signature, $resource, OPENSSL_ALGO_SHA256);
        if ($signature === '') {
            return '';
        }

        return $data . '.' . $this->base64UrlEncode($signature);
    }

    private function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    private function env(string $key): string
    {
        return trim((string)($_ENV[$key] ?? getenv($key) ?: ''));
    }
}
