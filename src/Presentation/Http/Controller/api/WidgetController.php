<?php

namespace app\Presentation\Http\Controller\api;

use app\Application\Assistant\Contract\AssistantRateLimiterInterface;
use app\Application\Assistant\Dto\AssistantRequestContext;
use app\Application\Assistant\Dto\BuildAssistantConfigurationRequest;
use app\Application\Assistant\Dto\LogAssistantOpenRequest;
use app\Application\Assistant\Exception\AssistantAccessDeniedException;
use app\Application\Assistant\Exception\AssistantContextNotFoundException;
use app\Application\Assistant\Exception\AssistantRateLimitExceededException;
use app\Application\Assistant\UseCase\BuildAssistantConfigurationUseCaseInterface;
use app\Application\Assistant\UseCase\LogAssistantOpenUseCaseInterface;
use app\Presentation\Http\Controller\ApiController;
use Yii;

class WidgetController extends ApiController
{
    public function __construct(
        $id,
        $module,
        private readonly BuildAssistantConfigurationUseCaseInterface $buildAssistantConfiguration,
        private readonly LogAssistantOpenUseCaseInterface $logAssistantOpen,
        private readonly AssistantRateLimiterInterface $assistantRateLimiter,
        $config = [],
    ) {
        parent::__construct($id, $module, $config);
    }

    public function actionConfiguration($publicKey): array
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $requestContext = $this->assistantRequestContext();

        try {
            $this->assistantRateLimiter->hit((int)$publicKey, $requestContext, 'configuration');

            return $this->buildAssistantConfiguration
                ->build(new BuildAssistantConfigurationRequest((int)$publicKey, $requestContext))
                ->toArray();
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (AssistantAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (AssistantRateLimitExceededException $e) {
            return $this->HTTPStatus(429, $e->getMessage());
        }
    }

    public function actionLogOpen($publicKey): array
    {
        $requestContext = $this->assistantRequestContext();

        try {
            $this->assistantRateLimiter->hit((int)$publicKey, $requestContext, 'log-open');
            $res = $this->logAssistantOpen->log(new LogAssistantOpenRequest((int)$publicKey, $requestContext));
        } catch (AssistantContextNotFoundException $e) {
            return $this->HTTPStatus(404, $e->getMessage());
        } catch (AssistantAccessDeniedException $e) {
            return $this->HTTPStatus(403, $e->getMessage());
        } catch (AssistantRateLimitExceededException $e) {
            return $this->HTTPStatus(429, $e->getMessage());
        } catch (\Exception $e) {
            return $this->HTTPStatus(504, 'Redis no connect ' . $e->getMessage());
        }

        return $this->HTTPStatus(200, 'Ok id=' . $res);
    }

    private function assistantRequestContext(): AssistantRequestContext
    {
        $request = Yii::$app->request;

        return new AssistantRequestContext(
            pathname: (string)$request->get('pathname', ''),
            getparams: (string)$request->get('getparams', ''),
            userId: $request->get('userId'),
            userRoles: (array)$request->get('userRole', []),
            stringRoles: $request->get('string_roles'),
            remoteAddr: $request->userIP ?? '0.0.0.0',
            originHost: $this->originHost(),
        );
    }

    private function originHost(): string
    {
        $headers = Yii::$app->request->headers;
        $origin = $headers->get('Origin') ?: $headers->get('Referer');

        return $origin ? (string)(parse_url($origin, PHP_URL_HOST) ?: '') : '';
    }
}
