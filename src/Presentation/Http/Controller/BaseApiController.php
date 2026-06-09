<?php

namespace app\Presentation\Http\Controller;

use app\Presentation\Http\ApiCorsBehavior;
use app\Presentation\Http\ApiResponseFactory;
use yii\rest\Controller;

abstract class BaseApiController extends Controller
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['corsFilter'] = ApiCorsBehavior::assistantApi();

        return $behaviors;
    }

    protected function responseFactory(): ApiResponseFactory
    {
        return new ApiResponseFactory();
    }

    protected function status(int $code, string|false $text = false): array
    {
        return $this->responseFactory()->status($code, $text);
    }
}
