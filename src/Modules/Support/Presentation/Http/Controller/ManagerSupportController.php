<?php

namespace app\Modules\Support\Presentation\Http\Controller;

use app\Application\Panel\ClientProjectService;
use app\Modules\Support\Application\Contract\SupportRealtimeTokenIssuerInterface;
use app\Modules\Support\Application\Service\SupportOperatorProjectAccessService;
use app\Modules\Support\Application\UseCase\ManageSupportEntryPointsUseCase;
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
        private readonly ManageSupportEntryPointsUseCase $entryPoints,
        private readonly OperatorSupportUseCase $operatorSupport,
        private readonly SupportRealtimeTokenIssuerInterface $realtimeTokenIssuer,
        private readonly ClientProjectService $projects,
        private readonly SupportOperatorProjectAccessService $projectAccess,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);

        if (Yii::$app->request->isPost) {
            if ($this->settings->saveFromPost($publicKey, Yii::$app->request->post())) {
                Yii::$app->session->setFlash('success', 'Настройки онлайн-поддержки сохранены');
                return $this->redirect($this->projectUrl('/manager/support', $projectId));
            }

            Yii::$app->session->setFlash('error', 'Не удалось сохранить настройки онлайн-поддержки');
        }

        return $this->render(
            '@app/src/Modules/Support/Presentation/Http/View/manager/settings',
            $this->settings->viewData($publicKey, (string)Yii::$app->user->identity->email)->toArray()
            + $this->projects->tabsData($ownerPublicKey, $projectId),
        );
    }

    public function actionConversations(): Response|string
    {
        $status = Yii::$app->request->get('status', 'open');

        return $this->render('@app/src/Modules/Support/Presentation/Http/View/manager/conversations', [
            'conversations' => $this->operatorSupport
                ->listConversationsForPublicKeys($this->projectPublicKeysForCurrentUser(), $status)
                ->toArray()['conversations'],
            'status' => $status,
        ]);
    }

    public function actionEntryPoints(): Response|string
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);

        if (Yii::$app->request->isPost) {
            try {
                if ($this->entryPoints->saveFromPost($publicKey, Yii::$app->request->post())) {
                    Yii::$app->session->setFlash('success', 'Кнопка быстрого обращения сохранена');
                    return $this->redirect($this->projectUrl('/manager/support/entry-points', $projectId));
                }

                Yii::$app->session->setFlash('error', 'Не удалось сохранить кнопку быстрого обращения');
            } catch (\Throwable $exception) {
                Yii::$app->session->setFlash('error', $exception->getMessage());
            }
        }

        return $this->render(
            '@app/src/Modules/Support/Presentation/Http/View/manager/entry-points',
            $this->entryPoints->viewData($publicKey)
            + $this->projects->tabsData($ownerPublicKey, $projectId),
        );
    }

    public function actionEntryPointDelete(): Response
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        $ownerPublicKey = Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        $publicKey = $this->projects->publicKeyForProject($ownerPublicKey, $projectId);
        $id = (int)Yii::$app->request->post('id', Yii::$app->request->get('id'));

        if ($id > 0 && $this->entryPoints->delete($publicKey, $id)) {
            Yii::$app->session->setFlash('success', 'Кнопка быстрого обращения удалена');
        } else {
            Yii::$app->session->setFlash('error', 'Не удалось удалить кнопку быстрого обращения');
        }

        return $this->redirect($this->projectUrl('/manager/support/entry-points', $projectId));
    }

    public function actionConversation(): Response|string
    {
        $conversationId = (int)Yii::$app->request->get('id');

        if ($conversationId <= 0) {
            Yii::$app->session->setFlash('error', 'Диалог не найден');
            return $this->redirect('/manager/support/conversations');
        }

        return $this->render('@app/src/Modules/Support/Presentation/Http/View/manager/conversation', [
            'conversationId' => $conversationId,
            'conversation' => $this->operatorSupport
                ->conversationForPublicKeys($this->projectPublicKeysForCurrentUser(), $conversationId)
                ->toArray()['conversation'],
            'messages' => $this->operatorSupport
                ->listMessagesForPublicKeys($this->projectPublicKeysForCurrentUser(), $conversationId)
                ->toArray()['messages'],
        ]);
    }

    public function actionReply(): Response
    {
        $conversationId = (int)Yii::$app->request->post('conversation_id');
        $body = (string)Yii::$app->request->post('body', '');
        $operatorId = (int)Yii::$app->user->id;

        try {
            $this->operatorSupport->replyForPublicKeys(
                $this->projectPublicKeysForCurrentUser(),
                $conversationId,
                $operatorId,
                $body,
            );
            Yii::$app->session->setFlash('success', 'Ответ отправлен');
        } catch (\Throwable $exception) {
            Yii::$app->session->setFlash('error', 'Не удалось отправить ответ');
        }

        return $this->redirect(['/manager/support/conversation', 'id' => $conversationId]);
    }

    public function actionConversationClose(): Response
    {
        if (!$this->isOwner()) {
            Yii::$app->session->setFlash('error', 'Закрывать диалог может только владелец аккаунта');
            return $this->redirect('/manager/support/conversations');
        }

        $publicKey = Yii::$app->user->identity->getPublicKey();
        $conversationId = (int)Yii::$app->request->post('id', Yii::$app->request->get('id'));

        if ($conversationId > 0) {
            try {
                $this->operatorSupport->closeConversation($publicKey, $conversationId);
                Yii::$app->session->setFlash('success', 'Диалог отправлен в архив');
            } catch (\Throwable $exception) {
                Yii::$app->session->setFlash('error', 'Не удалось отправить диалог в архив');
            }
        }

        return $this->redirect(['/manager/support/conversation', 'id' => $conversationId]);
    }

    public function actionConversationDelete(): Response
    {
        if (!$this->isOwner()) {
            Yii::$app->session->setFlash('error', 'Удалять диалог может только владелец аккаунта');
            return $this->redirect('/manager/support/conversations');
        }

        $publicKey = Yii::$app->user->identity->getPublicKey();
        $conversationId = (int)Yii::$app->request->post('id', Yii::$app->request->get('id'));

        if ($conversationId > 0) {
            try {
                $this->operatorSupport->deleteConversation($publicKey, $conversationId);
                Yii::$app->session->setFlash('success', 'Диалог удалён');
            } catch (\Throwable $exception) {
                Yii::$app->session->setFlash('error', 'Не удалось удалить диалог');
            }
        }

        return $this->redirect('/manager/support/conversations');
    }

    public function actionWsToken(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $publicKey = (int)Yii::$app->user->identity->getPublicKey();

        return [
            'token' => $this->realtimeTokenIssuer->issueManagerToken($publicKey),
            'publicKey' => $publicKey,
            'wsUrl' => $_ENV['DOMAINWSWIDGET'] ?? '',
        ];
    }

    private function projectUrl(string $path, ?int $projectId): string
    {
        return $projectId === null ? $path : $path . '?projectId=' . $projectId;
    }

    private function isOwner(): bool
    {
        return (int)Yii::$app->user->identity->getId() === (int)Yii::$app->user->identity->getPublicKey();
    }

    private function projectPublicKeysForCurrentUser(): array
    {
        return $this->projectAccess->publicKeysForOperator(
            (int)Yii::$app->user->identity->getPublicKey(),
            (int)Yii::$app->user->identity->getId(),
        );
    }
}
