<?php

namespace app\Modules\Support\Presentation\Http\Controller;

use app\Modules\Support\Application\UseCase\ManageSupportSettingsUseCase;
use app\Modules\Support\Application\UseCase\OperatorSupportUseCase;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\web\Response;

final class ManagerSupportController extends ManagerController
{
    public function __construct(
        $id,
        $module,
        private readonly ManageSupportSettingsUseCase $settings,
        private readonly OperatorSupportUseCase $operatorSupport,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): Response|string
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();

        if (Yii::$app->request->isPost) {
            if ($this->settings->saveFromPost($publicKey, Yii::$app->request->post())) {
                Yii::$app->session->setFlash('success', 'Настройки онлайн-поддержки сохранены');
                return $this->redirect('/manager/support');
            }

            Yii::$app->session->setFlash('error', 'Модуль онлайн-поддержки недоступен клиенту');
        }

        return $this->render('@app/src/Modules/Support/Presentation/Http/View/manager/settings', $this->settings->viewData($publicKey)->toArray());
    }

    public function actionConversations(): Response|string
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();
        $status = Yii::$app->request->get('status', 'open');

        return $this->render('@app/src/Modules/Support/Presentation/Http/View/manager/conversations', [
            'conversations' => $this->operatorSupport->listConversations($publicKey, $status)->toArray()['conversations'],
            'status' => $status,
        ]);
    }

    public function actionConversation(): Response|string
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();
        $conversationId = (int)Yii::$app->request->get('id');

        if ($conversationId <= 0) {
            Yii::$app->session->setFlash('error', 'Диалог не найден');
            return $this->redirect('/manager/support/conversations');
        }

        return $this->render('@app/src/Modules/Support/Presentation/Http/View/manager/conversation', [
            'conversationId' => $conversationId,
            'conversation' => $this->operatorSupport->conversation($publicKey, $conversationId)?->toArray()['conversation'],
            'messages' => $this->operatorSupport->listMessages($publicKey, $conversationId)->toArray()['messages'],
        ]);
    }

    public function actionReply(): Response
    {
        $publicKey = Yii::$app->user->identity->getPublicKey();
        $conversationId = (int)Yii::$app->request->post('conversation_id');
        $body = (string)Yii::$app->request->post('body', '');
        $operatorId = (int)Yii::$app->user->id;

        try {
            $this->operatorSupport->reply($publicKey, $conversationId, $operatorId, $body);
            Yii::$app->session->setFlash('success', 'Ответ отправлен');
        } catch (\Throwable $exception) {
            Yii::$app->session->setFlash('error', 'Не удалось отправить ответ');
        }

        return $this->redirect(['/manager/support/conversation', 'id' => $conversationId]);
    }
}
