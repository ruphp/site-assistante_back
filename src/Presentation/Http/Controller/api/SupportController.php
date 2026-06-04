<?php

namespace app\Presentation\Http\Controller\api;

use app\Application\Assistant\Exception\AssistantContextNotFoundException;
use app\Modules\Support\Application\Dto\GetSupportWidgetStateRequest;
use app\Modules\Support\Application\Dto\ListSupportMessagesRequest;
use app\Modules\Support\Application\Dto\SendSupportMessageRequest;
use app\Modules\Support\Application\Dto\StartSupportConversationRequest;
use app\Modules\Support\Application\Dto\SupportVisitorContext;
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

            return $this->startConversation
                ->start(new StartSupportConversationRequest(
                    (int)$publicKey,
                    $this->visitorContext(),
                    $this->stringBodyParam('message'),
                ))
                ->toArray();
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

    private function visitorContext(): SupportVisitorContext
    {
        $request = Yii::$app->request;

        return new SupportVisitorContext(
            visitorId: $request->get('visitorId') ?: $request->get('userId'),
            originHost: $this->originHost(),
            remoteAddr: $request->userIP ?? '0.0.0.0',
            pathname: (string)$request->get('pathname', ''),
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
        return Yii::$app->request->post($name, Yii::$app->request->get($name));
    }

    private function stringBodyParam(string $name): ?string
    {
        $value = $this->bodyParam($name);

        return $value === null ? null : (string)$value;
    }
}
