<?php

namespace app\Presentation\Http\Controller\api;

use app\Modules\SmsDelivery\Application\SmsDeliveryService;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

final class SmsDeliveryController extends Controller
{
    public $enableCsrfValidation = false;

    public function __construct(
        $id,
        $module,
        private readonly SmsDeliveryService $smsDelivery,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionCreateTask(): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $data = Yii::$app->request->getBodyParams();
            if (!is_array($data) || $data === []) {
                $data = json_decode(Yii::$app->request->getRawBody(), true);
            }

            Yii::$app->response->statusCode = 202;
            return $this->smsDelivery->createTask(
                $this->bearerToken(),
                is_array($data) ? $data : [],
                (string)Yii::$app->request->userIP,
            );
        } catch (\DomainException $exception) {
            return $this->error(403, $exception->getMessage());
        } catch (\InvalidArgumentException $exception) {
            return $this->error(422, $exception->getMessage());
        } catch (\OverflowException $exception) {
            return $this->error(429, $exception->getMessage());
        } catch (\Throwable $exception) {
            Yii::error($exception, 'sms-delivery-api');
            return $this->error(500, 'Не удалось создать задание');
        }
    }

    public function actionStatus(string $requestId): array
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return $this->smsDelivery->taskStatus($this->bearerToken(), $requestId);
        } catch (\DomainException $exception) {
            return $this->error(403, $exception->getMessage());
        } catch (\RuntimeException $exception) {
            return $this->error(404, $exception->getMessage());
        } catch (\Throwable $exception) {
            Yii::error($exception, 'sms-delivery-api');
            return $this->error(500, 'Не удалось получить статус');
        }
    }

    private function bearerToken(): string
    {
        return (string)Yii::$app->request->headers->get('Authorization', '');
    }

    private function error(int $status, string $message): array
    {
        Yii::$app->response->statusCode = $status;
        return ['success' => false, 'message' => $message];
    }
}
