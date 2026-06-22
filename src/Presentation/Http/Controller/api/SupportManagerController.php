<?php

namespace app\Presentation\Http\Controller\api;

use app\Infrastructure\User\UserIdentity;
use app\Modules\Support\Application\Exception\SupportAccessDeniedException;
use app\Modules\Support\Infrastructure\RedisSupportRealtimePublisher;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord;
use app\Modules\Support\Infrastructure\YiiSupportConversationRepository;
use app\Presentation\Http\MobileTokenAuth;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class SupportManagerController extends Controller
{
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

            $conversationId = (int)Yii::$app->request->post('conversationId');
            $body = trim(Yii::$app->request->post('body', ''));

            if (!$conversationId || !$body) {
                return ['success' => false, 'message' => 'Заполните все поля'];
            }

            $conversation = SupportConversationRecord::findOne([
                'id' => $conversationId,
                'public_key' => $user->public_key,
            ]);
            if (!$conversation instanceof SupportConversationRecord) {
                return $this->errorResponse(404, 'Диалог не найден');
            }

            $message = new SupportMessageRecord();
            $message->public_key = $user->public_key;
            $message->conversation_id = $conversationId;
            $message->visitor_id = '';
            $message->body = $body;
            $message->sender_type = 'manager';
            $message->save(false);

            $conversation->updated_at = date('Y-m-d H:i:s');
            $conversation->save(false);

            try {
                $publisher = new RedisSupportRealtimePublisher();
                $publisher->publishMessage(
                    new \app\Modules\Support\Domain\SupportConversation(
                        id: (int)$conversation->id,
                        publicKey: (int)$conversation->public_key,
                        visitorId: (string)$conversation->visitor_id,
                        visitorName: $conversation->visitor_name === null ? null : (string)$conversation->visitor_name,
                        visitorEmail: $conversation->visitor_email === null ? null : (string)$conversation->visitor_email,
                        pageUrl: $conversation->page_url === null ? null : (string)$conversation->page_url,
                        status: (string)$conversation->status,
                        createdAt: $conversation->created_at === null ? null : (string)$conversation->created_at,
                        lastMessageAt: null,
                        lastSenderType: null,
                        operatorRepliedAt: $conversation->operator_replied_at === null ? null : (string)$conversation->operator_replied_at,
                        operatorSeenAt: $conversation->operator_seen_at === null ? null : (string)$conversation->operator_seen_at,
                        lastVisitorActivityAt: $conversation->last_visitor_activity_at === null ? null : (string)$conversation->last_visitor_activity_at,
                        entryPointId: $conversation->entry_point_id === null ? null : (int)$conversation->entry_point_id,
                        entryPointTitle: null,
                        priority: (int)$conversation->priority,
                    ),
                    new \app\Modules\Support\Domain\SupportMessage(
                        id: (int)$message->id,
                        conversationId: (int)$message->conversation_id,
                        publicKey: (int)$message->public_key,
                        senderType: (string)$message->sender_type,
                        senderId: $message->sender_id === null ? null : (string)$message->sender_id,
                        body: (string)$message->body,
                        createdAt: $message->created_at === null ? null : (string)$message->created_at,
                    ),
                );
            } catch (\Throwable) {
            }

            return [
                'success' => true,
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'senderType' => $message->sender_type,
                    'createdAt' => $message->created_at,
                ],
            ];
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), 'support-manager');
            return $this->errorResponse(500, 'Не удалось отправить сообщение');
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
}
