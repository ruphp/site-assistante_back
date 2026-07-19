<?php

namespace app\Presentation\Http\Controller\api;

use app\Modules\Support\Application\Bot\TelegramManagerBotService;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

final class TelegramManagerBotController extends Controller
{
    public $enableCsrfValidation = false;

    public function __construct(
        $id,
        $module,
        private readonly TelegramManagerBotService $bot,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionWebhook(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!$this->secretIsValid()) {
            Yii::$app->response->statusCode = 403;
            return ['ok' => false];
        }

        $payload = json_decode(Yii::$app->request->getRawBody(), true);
        if (is_array($payload)) {
            $this->bot->handleUpdate($payload);
        }

        return ['ok' => true];
    }

    private function secretIsValid(): bool
    {
        $secret = trim((string)($_ENV['TELEGRAM_MANAGER_BOT_SECRET'] ?? ''));
        if ($secret === '') {
            return true;
        }

        $header = Yii::$app->request->headers->get('X-Telegram-Bot-Api-Secret-Token', '');
        $query = Yii::$app->request->get('secret', '');

        return hash_equals($secret, (string)$header) || hash_equals($secret, (string)$query);
    }
}
