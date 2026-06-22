<?php

namespace app\Presentation\Http\Controller\api;

use app\Application\Assistant\Exception\AssistantContextNotFoundException;
use app\Modules\Support\Application\Dto\GetSupportWidgetStateRequest;
use app\Modules\Support\Application\Dto\ListSupportMessagesRequest;
use app\Modules\Support\Application\Dto\SendSupportMessageRequest;
use app\Modules\Support\Application\Dto\StartSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportConversationResponse;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
use app\Modules\Support\Application\Contract\SupportConversationRepositoryInterface;
use app\Modules\Support\Application\Exception\SupportAccessDeniedException;
use app\Modules\Support\Application\Exception\SupportConversationNotFoundException;
use app\Modules\Support\Application\Exception\SupportLimitExceededException;
use app\Modules\Support\Application\UseCase\GetSupportWidgetStateUseCaseInterface;
use app\Modules\Support\Application\UseCase\ListSupportMessagesUseCaseInterface;
use app\Modules\Support\Application\UseCase\SendSupportMessageUseCaseInterface;
use app\Modules\Support\Application\UseCase\StartSupportConversationUseCaseInterface;
use app\Presentation\Http\Controller\ApiController;
use Yii;
use yii\web\Response;

class SupportController extends ApiController
{
    public function __construct(
        $id,
        $module,
        private readonly GetSupportWidgetStateUseCaseInterface $getState,
        private readonly StartSupportConversationUseCaseInterface $startConversation,
        private readonly SendSupportMessageUseCaseInterface $sendMessage,
        private readonly ListSupportMessagesUseCaseInterface $listMessages,
        private readonly SupportConversationRepositoryInterface $conversations,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionState($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $this->getState
                ->get(new GetSupportWidgetStateRequest((int)$publicKey, $this->visitorContext()))
                ->toArray();
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        }
    }

    public function actionStartConversation($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $context = $this->visitorContext();
            $anonymous = (bool)$this->bodyParam('anonymous');
            $visitorEmail = trim((string)$context->visitorEmail);

            if (!$anonymous && $visitorEmail !== '') {
                $openConversation = $this->conversations->findOpenByEmail((int)$publicKey, $visitorEmail);
                if ($openConversation !== null) {
                    $verification = $this->issueConversationVerification((int)$publicKey, $visitorEmail, $openConversation);
                    Yii::$app->response->setStatusCode(409);

                    return [
                        'code' => 409,
                        'error' => 'Verification required',
                        'verification' => [
                            'email' => $this->maskedEmail($visitorEmail),
                            'expires_in' => $verification['expires_in'],
                            'conversation' => (new SupportConversationResponse($openConversation))->toArray()['conversation'],
                        ],
                    ];
                }
            }

            return $this->startConversation
                ->start(new StartSupportConversationRequest(
                    (int)$publicKey,
                    $context,
                    $this->stringBodyParam('message'),
                    $this->bodyParam('entry_point_id') === null ? null : (int)$this->bodyParam('entry_point_id'),
                ))
                ->toArray();
        } catch (\InvalidArgumentException $e) {
            return $this->HTTPStatus(400, $e->getMessage());
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (SupportLimitExceededException $e) {
            return $this->HTTPStatus(429, $e->getMessage());
        }
    }

    public function actionSendMessage($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $this->sendMessage
                ->send(new SendSupportMessageRequest(
                    (int)$publicKey,
                    (int)$this->bodyParam('conversation_id'),
                    $this->visitorContext(),
                    (string)$this->bodyParam('message'),
                ))
                ->toArray();
        } catch (\InvalidArgumentException $e) {
            return $this->HTTPStatus(400, $e->getMessage());
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportConversationNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (SupportLimitExceededException $e) {
            return $this->HTTPStatus(429, $e->getMessage());
        }
    }

    public function actionMessages($publicKey, $conversationId): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            return $this->listMessages
                ->list(new ListSupportMessagesRequest(
                    (int)$publicKey,
                    (int)$conversationId,
                    $this->visitorContext(),
                    Yii::$app->request->get('after_id') === null ? null : (int)Yii::$app->request->get('after_id'),
                ))
                ->toArray();
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportConversationNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        }
    }

    public function actionVerifyConversation($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $email = strtolower(trim((string)$this->bodyParam('email')));
            $code = trim((string)$this->bodyParam('code'));

            if ($email === '' || $code === '') {
                return $this->HTTPStatus(400, 'Email and code are required');
            }

            $conversation = $this->conversations->findOpenByEmail((int)$publicKey, $email);
            if ($conversation === null) {
                return $this->HTTPStatus(404, 'Conversation not found');
            }

            if (!$this->validateConversationVerification((int)$publicKey, $email, $code, (int)$conversation->id)) {
                return $this->HTTPStatus(422, 'Invalid code');
            }

            return (new SupportConversationResponse($conversation))->toArray();
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support');
            return $this->HTTPStatus(500, 'Не удалось подтвердить код');
        }
    }

    public function actionActivity($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $conversationId = (int)$this->bodyParam('conversation_id');
            $event = trim((string)$this->bodyParam('event'));

            if ($conversationId <= 0 || $event === '') {
                return $this->HTTPStatus(400, 'Conversation id and event are required');
            }

            $conversation = $this->conversations->getForClient((int)$publicKey, $conversationId);
            if ($conversation === null) {
                return $this->HTTPStatus(404, 'Conversation not found');
            }

            switch ($event) {
                case 'visitor_activity':
                    $this->conversations->markVisitorActivity((int)$publicKey, $conversationId);
                    break;
                case 'operator_seen':
                    $this->conversations->markOperatorSeen((int)$publicKey, $conversationId);
                    break;
                default:
                    return $this->HTTPStatus(400, 'Unsupported event');
            }

            return [
                'success' => true,
            ];
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support');
            return $this->HTTPStatus(500, 'Не удалось сохранить событие');
        }
    }

    public function actionCloseConversation($publicKey): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $conversationId = (int)$this->bodyParam('conversation_id');
            if ($conversationId <= 0) {
                return $this->HTTPStatus(400, 'Conversation id is required');
            }

            $visitorId = $this->visitorContext()->resolvedVisitorId();
            $conversation = $this->conversations->getOpenForVisitor((int)$publicKey, $conversationId, $visitorId);
            if ($conversation === null) {
                return $this->HTTPStatus(404, 'Conversation not found');
            }

            $this->conversations->closeForClient((int)$publicKey, $conversationId);

            return [
                'success' => true,
            ];
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (SupportAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support');
            return $this->HTTPStatus(500, 'Не удалось завершить диалог');
        }
    }

    private function visitorContext(): SupportVisitorContext
    {
        $request = Yii::$app->request;

        $visitorEmail = $this->firstRequestValue($request, ['visitorEmail', 'userEmail', 'visitor_email', 'user_email', 'email'])
            ?? $this->bodyFirstRequestValue($request, ['visitorEmail', 'userEmail', 'visitor_email', 'user_email', 'email']);

        $visitorName = $this->firstRequestValue($request, ['visitorName', 'userName', 'visitor_name', 'user_name', 'name'])
            ?? $this->bodyFirstRequestValue($request, ['visitorName', 'userName', 'visitor_name', 'user_name', 'name']);

        $visitorId = $this->firstRequestValue($request, ['visitorId', 'userId', 'visitor_id', 'user_id', 'id'])
            ?? $this->bodyFirstRequestValue($request, ['visitorId', 'userId', 'visitor_id', 'user_id', 'id']);

        return new SupportVisitorContext(
            visitorId: $visitorId === null ? null : (string)$visitorId,
            visitorName: $visitorName === null ? null : (string)$visitorName,
            visitorEmail: $visitorEmail === null ? null : (string)$visitorEmail,
            originHost: $this->originHost(),
            remoteAddr: $request->userIP ?? '0.0.0.0',
            pathname: (string)$request->get('pathname', ''),
            pageUrl: (string)$request->get('pageUrl', ''),
        );
    }

    private function originHost(): string
    {
        $headers = Yii::$app->request->headers;
        $origin = $headers->get('Origin') ?: $headers->get('Referer');

        return $origin ? (string)(parse_url($origin, PHP_URL_HOST) ?: '') : '';
    }

    private function bodyParam(string $name): mixed
    {
        $request = Yii::$app->request;
        $postValue = $request->post($name);
        if ($postValue !== null) {
            return $postValue;
        }

        $body = $request->getRawBody();
        if ($body !== '') {
            $decoded = json_decode($body, true);
            if (is_array($decoded) && array_key_exists($name, $decoded)) {
                return $decoded[$name];
            }
        }

        return $request->get($name);
    }

    private function firstRequestValue(\yii\web\Request $request, array $names): mixed
    {
        foreach ($names as $name) {
            $value = $request->get($name);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function bodyFirstRequestValue(\yii\web\Request $request, array $names): mixed
    {
        foreach ($names as $name) {
            $value = $this->bodyParam($name);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }

        return null;
    }

    private function stringBodyParam(string $name): ?string
    {
        $value = $this->bodyParam($name);

        return $value === null ? null : (string)$value;
    }

    private function verificationCacheKey(int $publicKey, string $email): string
    {
        return 'support:verification:' . $publicKey . ':' . sha1(mb_strtolower(trim($email)));
    }

    private function issueConversationVerification(int $publicKey, string $email, $conversation): array
    {
        $code = (string)random_int(100000, 999999);
        $expiresIn = 600;

        Yii::$app->cache->set($this->verificationCacheKey($publicKey, $email), [
            'code' => $code,
            'conversation_id' => (int)$conversation->id,
            'email' => mb_strtolower(trim($email)),
        ], $expiresIn);

        $body = implode("\n", [
            'Здравствуйте!',
            '',
            'Для продолжения ранее начатого диалога введите код:',
            $code,
            '',
            'Код действует 10 минут.',
        ]);

        try {
            Yii::$app->mailer
                ->compose()
                ->setTo($email)
                ->setFrom([$_ENV['MAIL_USER'] ?? ($_ENV['adminEmail'] ?? 'admin@sitewidget.ru') => 'SiteWidget'])
                ->setSubject('Код доступа к диалогу SiteWidget')
                ->setTextBody($body)
                ->send();
        } catch (\Throwable) {
        }

        return [
            'code' => $code,
            'expires_in' => $expiresIn,
        ];
    }

    private function validateConversationVerification(int $publicKey, string $email, string $code, int $conversationId): bool
    {
        $key = $this->verificationCacheKey($publicKey, $email);
        $payload = Yii::$app->cache->get($key);

        if (!is_array($payload)) {
            return false;
        }

        $storedCode = trim((string)($payload['code'] ?? ''));
        $storedConversationId = (int)($payload['conversation_id'] ?? 0);
        $storedEmail = strtolower(trim((string)($payload['email'] ?? '')));

        if ($storedCode === '' || $storedConversationId !== $conversationId || $storedEmail !== strtolower(trim($email))) {
            return false;
        }

        $valid = hash_equals($storedCode, trim($code));
        if ($valid) {
            Yii::$app->cache->delete($key);
        }

        return $valid;
    }

    private function maskedEmail(string $email): string
    {
        $email = trim($email);
        if ($email === '' || !str_contains($email, '@')) {
            return $email;
        }

        [$local, $domain] = explode('@', $email, 2);
        $visible = mb_substr($local, 0, 2);

        return $visible . str_repeat('*', max(0, mb_strlen($local) - 2)) . '@' . $domain;
    }
}
