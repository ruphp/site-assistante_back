<?php

namespace app\Presentation\Http\Controller\api;

use app\Presentation\Http\MobileTokenAuth;
use Yii;
use yii\rest\Controller;
use app\Modules\Support\Infrastructure\YiiSupportConversationRepository;
use app\Modules\Support\Infrastructure\YiiSupportMessageRepository;
use app\Modules\Support\Infrastructure\RedisSupportRealtimePublisher;
use app\Modules\Support\Infrastructure\YiiActiveRecord\SupportMessageRecord;

class SupportManagerController extends Controller
{
    public function behaviors()
    {
        return [
            'tokenAuth' => [
                'class' =>  MobileTokenAuth::class,
            ],
        ];
    }

    public function actionConversations()
    {
        $user = Yii::$app->user->identity;
        $repo = new YiiSupportConversationRepository();
        $list = $repo->listForClient($user->public_key, 'open', 50);

        return array_map(function ($c) {
            return [
                'id' => $c->id,
                'visitorId' => $c->visitorId,
                'visitorEmail' => $c->visitorEmail,
                'pageUrl' => $c->pageUrl,
                'status' => $c->status,
                'lastMessageAt' => $c->lastMessageAt,
                'lastSenderType' => $c->lastSenderType,
                'priority' => $c->priority,
                'waitsForOperator' => $c->waitsForOperator(),
            ];
        }, $list);
    }

    public function actionMessages($conversationId)
    {
        $user = Yii::$app->user->identity;
        $messages = SupportMessageRecord::find()
            ->where([
                'public_key' => $user->public_key,
                'conversation_id' => (int) $conversationId,
            ])
            ->orderBy(['id' => SORT_ASC])
            ->all();

        return array_map(fn($m) => [
            'id' => $m->id,
            'body' => $m->body,
            'senderType' => $m->sender_type,
            'createdAt' => $m->created_at,
        ], $messages);
    }

    public function actionSendMessage()
    {
        $user = Yii::$app->user->identity;
        $conversationId = (int) Yii::$app->request->post('conversationId');
        $body = trim(Yii::$app->request->post('body', ''));

        if (!$conversationId || !$body) {
            return ['success' => false, 'message' => 'Заполните все поля'];
        }

        $message = new SupportMessageRecord();
        $message->public_key = $user->public_key;
        $message->conversation_id = $conversationId;
        $message->visitor_id = '';
        $message->body = $body;
        $message->sender_type = 'manager';
        $message->save(false);

        // Обновить время диалога
        $conversation = \app\Modules\Support\Infrastructure\YiiActiveRecord\SupportConversationRecord::findOne([
            'id' => $conversationId,
            'public_key' => $user->public_key,
        ]);
        if ($conversation) {
            $conversation->updated_at = date('Y-m-d H:i:s');
            $conversation->save(false);
        }

        // Публикуем в Redis для WebSocket
        try {
            $publisher = new RedisSupportRealtimePublisher();
            $publisher->publish([
                'type' => 'support.message',
                'publicKey' => $user->public_key,
                'conversationId' => $conversationId,
                'visitorId' => $conversation->visitor_id ?? '',
                'message' => [
                    'id' => $message->id,
                    'body' => $message->body,
                    'senderType' => $message->sender_type,
                    'createdAt' => $message->created_at,
                ],
            ]);
        } catch (\Exception $e) {
            // молча — сообщение уже сохранено
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
    }
}