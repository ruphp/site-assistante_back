<?php

namespace app\Presentation\Http\Controller\api;

use app\Infrastructure\User\UserIdentity;
use app\Modules\Support\Application\Exception\SupportAccessDeniedException;
use app\Modules\Support\Application\Contract\SupportPushDeviceRepositoryInterface;
use app\Modules\Support\Application\Contract\SupportPushNotificationSenderInterface;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;
use app\Modules\Support\Infrastructure\YiiSupportConversationRepository;
use app\Modules\Support\Application\UseCase\OperatorSupportUseCase;
use app\Presentation\Http\MobileTokenAuth;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class SupportManagerController extends Controller
{
    public function __construct(
        $id,
        $module,
        private readonly OperatorSupportUseCase $operatorSupport,
        private readonly SupportPushDeviceRepositoryInterface $pushDevices,
        private readonly SupportPushNotificationSenderInterface $pushSender,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function behaviors()
    {
        return [
            'tokenAuth' => [
                'class' => MobileTokenAuth::class,
            ],
        ];
    }

    public function actionConversations(): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $user = Yii::$app->user->identity;
            if (!$user instanceof UserIdentity) {
                return $this->errorResponse(401, 'Токен недействителен');
            }

            $repo = new YiiSupportConversationRepository();
            $status = Yii::$app->request->get('status');
            if ($status === '' || $status === 'all') {
                $status = null;
            }

            $timeoutMinutes = (int)($_ENV['SUPPORT_AUTO_CLOSE_AFTER_OPERATOR_SEEN_MINUTES'] ?? 30);
            $repo->closeExpiredAfterOperatorSeen(max(60, $timeoutMinutes * 60));

            $list = $repo->listForClient($user->public_key, $status, 50);

            return array_map(static function ($c) {
                return [
                    'id' => $c->id,
                    'visitorId' => $c->visitorId,
                    'visitorName' => $c->visitorName,
                    'visitorEmail' => $c->visitorEmail,
                    'pageUrl' => $c->pageUrl,
                    'status' => $c->status,
                    'entryPointTitle' => $c->entryPointTitle,
                    'lastMessageAt' => $c->lastMessageAt,
                    'lastSenderType' => $c->lastSenderType,
                    'priority' => $c->priority,
                    'waitsForOperator' => $c->waitsForOperator(),
                ];
            }, $list);
        } catch (SupportAccessDeniedException $e) {
            return $this->errorResponse(403, $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось загрузить диалоги');
        }
    }

    public function actionMessages($conversationId): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $user = Yii::$app->user->identity;
            if (!$user instanceof UserIdentity) {
                return $this->errorResponse(401, 'Токен недействителен');
            }

            $messages = SupportMessageRecord::find()
                ->where([
                    'public_key' => $user->public_key,
                    'conversation_id' => (int)$conversationId,
                ])
                ->orderBy(['id' => SORT_ASC])
                ->all();

            return array_map(static fn($m) => [
                'id' => $m->id,
                'body' => $m->body,
                'senderType' => $m->sender_type,
                'createdAt' => $m->created_at,
            ], $messages);
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось загрузить сообщения');
        }
    }

    public function actionSendMessage(): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $user = Yii::$app->user->identity;
            if (!$user instanceof UserIdentity) {
                return $this->errorResponse(401, 'Токен недействителен');
            }

            $data = $this->requestData();
            $conversationId = (int)($data['conversationId'] ?? $data['conversation_id'] ?? 0);
            $body = trim((string)($data['body'] ?? ''));

            if (!$conversationId || !$body) {
                return $this->errorResponse(400, 'Заполните все поля');
            }

            $message = $this->operatorSupport->reply(
                (int)$user->public_key,
                $conversationId,
                (int)$user->id,
                $body,
            );

            return [
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'senderType' => $message->senderType,
                    'createdAt' => $message->createdAt,
                ],
            ];
        } catch (SupportAccessDeniedException $e) {
            return $this->errorResponse(403, $e->getMessage());
        } catch (\InvalidArgumentException $e) {
            return $this->errorResponse(400, $e->getMessage());
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось отправить сообщение');
        }
    }

    public function actionDeviceToken(): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $user = Yii::$app->user->identity;
            if (!$user instanceof UserIdentity) {
                return $this->errorResponse(401, 'Токен недействителен');
            }

            $data = $this->requestData();
            $token = trim((string)($data['token'] ?? ''));
            $platform = trim((string)($data['platform'] ?? 'android'));

            if ($token === '') {
                return $this->errorResponse(400, 'Токен устройства не передан');
            }

            $this->pushDevices->upsertForUser($user, $token, $platform);

            return [
                'success' => true,
                'push_enabled' => $this->pushSender->isConfigured(),
            ];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось сохранить токен устройства');
        }
    }

    public function actionDeviceTokenDelete(): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $data = $this->requestData();
            $token = trim((string)($data['token'] ?? ''));
            if ($token === '') {
                return $this->errorResponse(400, 'Токен устройства не передан');
            }

            $this->pushDevices->deactivateByToken($token);

            return [
                'success' => true,
            ];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось удалить токен устройства');
        }
    }

    private function errorResponse(int $status, string $message): array
    {
        Yii::$app->response->statusCode = $status;

        return [
            'success' => false,
            'message' => $message,
        ];
    }

    private function requestData(): array
    {
        $data = Yii::$app->request->getBodyParams();
        if (is_array($data) && $data !== []) {
            return $data;
        }

        $raw = Yii::$app->request->getRawBody();
        if ($raw === '') {
            return Yii::$app->request->post();
        }

        $decoded = json_decode($raw, true);
        return is_array($decoded) ? $decoded : Yii::$app->request->post();
    }
}
