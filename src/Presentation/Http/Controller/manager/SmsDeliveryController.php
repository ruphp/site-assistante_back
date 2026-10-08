<?php

namespace app\Presentation\Http\Controller\manager;

use app\Application\Panel\ClientProjectService;
use app\Modules\SmsDelivery\Application\SmsDeliveryService;
use app\Presentation\Http\Controller\ManagerController;
use Yii;
use yii\web\Response;

final class SmsDeliveryController extends ManagerController
{
    public function __construct(
        $id,
        $module,
        private readonly ClientProjectService $projects,
        private readonly SmsDeliveryService $smsDelivery,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionIndex(): string|Response
    {
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$ownerPublicKey, $projectId, $publicKey] = $this->context();
        return $this->render('index', [
            'settings' => $this->smsDelivery->projectSettings($publicKey),
            'newToken' => Yii::$app->session->getFlash('smsDeliveryToken'),
            'newCallbackSecret' => Yii::$app->session->getFlash('smsDeliveryCallbackSecret'),
        ] + $this->projects->tabsData($ownerPublicKey, $projectId));
    }

    public function actionToken(): Response
    {
        Yii::$app->request->validateCsrfToken();
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$ownerPublicKey, $projectId, $publicKey] = $this->context();
        try {
            $credentials = $this->smsDelivery->rotateToken($publicKey);
            Yii::$app->session->setFlash('smsDeliveryToken', $credentials['token']);
            Yii::$app->session->setFlash('smsDeliveryCallbackSecret', $credentials['callbackSecret']);
            Yii::$app->session->setFlash('success', 'Новый API-токен создан. Сохраните его сейчас: повторно он не показывается.');
        } catch (\Throwable $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }

        return $this->redirect(['/manager/sms-delivery', 'projectId' => $projectId]);
    }

    public function actionSave(): Response
    {
        Yii::$app->request->validateCsrfToken();
        if (!$this->isOwner()) {
            return $this->redirect('/manager/support/conversations');
        }

        [$ownerPublicKey, $projectId, $publicKey] = $this->context();
        try {
            $this->smsDelivery->saveSettings(
                $publicKey,
                (string)Yii::$app->request->post('callbackUrl', ''),
                (bool)Yii::$app->request->post('enabled', false),
            );
            Yii::$app->session->setFlash('success', 'Настройки SMS-кодов сохранены');
        } catch (\Throwable $exception) {
            Yii::$app->session->setFlash('error', $exception->getMessage());
        }

        return $this->redirect(['/manager/sms-delivery', 'projectId' => $projectId]);
    }

    private function context(): array
    {
        $ownerPublicKey = (int)Yii::$app->user->identity->getPublicKey();
        $projectId = (int)Yii::$app->request->get('projectId') ?: null;
        return [$ownerPublicKey, $projectId, $this->projects->publicKeyForProject($ownerPublicKey, $projectId)];
    }

    private function isOwner(): bool
    {
        return (int)Yii::$app->user->id === (int)Yii::$app->user->identity->getPublicKey();
    }
}
