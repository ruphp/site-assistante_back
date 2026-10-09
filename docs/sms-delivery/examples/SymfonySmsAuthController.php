<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Cache\CacheItemPoolInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Uid\Uuid;

final class SmsCodeRequest
{
    public function __construct(public readonly string $phone = '') {}
}

final class SmsCodeVerifyRequest
{
    public function __construct(
        public readonly string $requestId = '',
        public readonly string $code = '',
    ) {}
}

final class SymfonySmsAuthController extends AbstractController
{
    public function __construct(
        private readonly HttpClientInterface $http,
        private readonly CacheItemPoolInterface $cache,
        private readonly RateLimiterFactory $smsRequestLimiter,
        #[Autowire('%env(SITEWIDGET_SMS_TOKEN)%')]
        private readonly string $siteWidgetToken,
        #[Autowire('%kernel.secret%')]
        private readonly string $appSecret,
    ) {
    }

    #[Route('/api/registration/phone/request', methods: ['POST'])]
    public function requestCode(#[MapRequestPayload] SmsCodeRequest $input, Request $request): JsonResponse
    {
        $phone = $this->normalizePhone($input->phone);
        if ($phone === null) {
            return $this->json(['message' => 'Некорректный номер телефона'], 422);
        }

        $limit = $this->smsRequestLimiter
            ->create(($request->getClientIp() ?? 'unknown') . ':' . $phone)
            ->consume(1);
        if (!$limit->isAccepted()) {
            return $this->json(['message' => 'Слишком много запросов кода'], 429);
        }

        $requestId = Uuid::v7()->toRfc4122();
        $code = (string)random_int(1000, 9999);
        $expiresAt = new \DateTimeImmutable('+5 minutes');
        $cacheKey = 'sms_auth_' . str_replace('-', '_', $requestId);
        $item = $this->cache->getItem($cacheKey);
        $item->set([
            'phone' => $phone,
            'codeHash' => hash_hmac('sha256', $code, $this->appSecret),
            'attempts' => 0,
        ]);
        $item->expiresAt($expiresAt);
        $this->cache->save($item);

        try {
            $response = $this->http->request('POST', 'https://sitewidget.ru/api/sms/v1/tasks', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->siteWidgetToken,
                    'Accept' => 'application/json',
                ],
                'json' => [
                    'requestId' => $requestId,
                    'phone' => $phone,
                    'text' => 'Код подтверждения ShopsBox: ' . $code,
                    'expiresAt' => $expiresAt->format(DATE_ATOM),
                ],
                'timeout' => 5,
            ]);
            if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
                throw new \RuntimeException('SiteWidget rejected SMS task');
            }
        } catch (\Throwable) {
            $this->cache->deleteItem($cacheKey);
            return $this->json(['message' => 'Не удалось передать запрос на отправку SMS'], 502);
        }

        return $this->json([
            'requestId' => $requestId,
            'expiresIn' => 300,
        ], 202);
    }

    #[Route('/api/registration/phone/verify', methods: ['POST'])]
    public function verifyCode(#[MapRequestPayload] SmsCodeVerifyRequest $input): JsonResponse
    {
        if (!Uuid::isValid($input->requestId) || !preg_match('/^\d{4}$/', $input->code)) {
            return $this->json(['message' => 'Некорректный код'], 422);
        }

        $cacheKey = 'sms_auth_' . str_replace('-', '_', $input->requestId);
        $item = $this->cache->getItem($cacheKey);
        if (!$item->isHit() || !is_array($data = $item->get())) {
            return $this->json(['message' => 'Код истёк или не найден'], 410);
        }

        $attempts = (int)($data['attempts'] ?? 0) + 1;
        if ($attempts > 5) {
            $this->cache->deleteItem($cacheKey);
            return $this->json(['message' => 'Превышено число попыток'], 429);
        }

        $expectedHash = (string)($data['codeHash'] ?? '');
        $actualHash = hash_hmac('sha256', $input->code, $this->appSecret);
        if (!hash_equals($expectedHash, $actualHash)) {
            $data['attempts'] = $attempts;
            $item->set($data);
            $this->cache->save($item);
            return $this->json(['message' => 'Неверный код'], 422);
        }

        $this->cache->deleteItem($cacheKey);

        // Здесь ShopsBox продолжает регистрацию и сохраняет phone_verified_at для нового пользователя.
        // При последующих входах повторный SMS-код не требуется.
        return $this->json(['verified' => true, 'phone' => $data['phone']]);
    }

    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (strlen($digits) === 11 && $digits[0] === '8') {
            $digits = '7' . substr($digits, 1);
        }

        return strlen($digits) >= 10 && strlen($digits) <= 15 ? '+' . $digits : null;
    }
}
