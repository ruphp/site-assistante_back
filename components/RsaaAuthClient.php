<?php

namespace app\components;

use Yii;
use yii\authclient\OAuth2;
use yii\authclient\OAuthToken;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\HttpException;

class RsaaAuthClient extends OAuth2
{



    //public $tokenUrl = (string)Yii::$app->params['tokenUrl'];
    //public $authUrl = (string)Yii::$app->params['authUrl'];

    //public $apiBaseUrl = 'https://sso.dev.nspd.rosreestr.gov.ru/oauth2/v2';

    public $enablePkce = false;

    public $scope = '';
    public $roles = null;
    public $attributeNames = [
    ];

    public function buildAuthUrl(array $params = [])
    {
        Yii::warning('buildAuthUrl', 'rsaa ');


        $defaultParams = [
            'client_id' => $this->clientId,
            'response_type' => 'code',
            'redirect_uri' => $_ENV['RSAA_REDIRECT_URI'],
        ];
        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        if ($this->validateAuthState) {
            $authState = $this->generateAuthState();
            $this->setState('authState', $authState);
            $defaultParams['state'] = $authState;
        }

        if ($this->enablePkce) {
            $codeVerifier = bin2hex(Yii::$app->security->generateRandomKey(64));
            $this->setState('authCodeVerifier', $codeVerifier);
            $defaultParams['code_challenge'] = trim(strtr(base64_encode(hash('sha256', $codeVerifier, true)), '+/', '-_'), '=');
            $defaultParams['code_challenge_method'] = 'S256';
        }

        return $this->composeUrl($this->authUrl, array_merge($defaultParams, $params));
    }


    public function fetchAccessToken($authCode, array $params = [])
    {
        Yii::warning('fetchAccessToken', 'rsaa ');

        if ($this->validateAuthState) {
            $authState = $this->getState('authState');
            $incomingRequest = Yii::$app->getRequest();
            $incomingState = $incomingRequest->get('state', $incomingRequest->post('state'));
            if (
                !isset($incomingState)
                || empty($authState)
                || !Yii::$app->getSecurity()->compareString($incomingState, $authState)
            ) {
                throw new HttpException(400, 'Invalid auth state parameter.');
            }
            $this->removeState('authState');
        }

        $defaultParams = [
            'code' => $authCode,
            'grant_type' => 'authorization_code',
            'redirect_uri' => $_ENV['RSAA_REDIRECT_URI'],
        ];
        Yii::warning($defaultParams, 'rsaa Params ');
        if ($this->enablePkce) {
            $authCodeVerifier = $this->getState('authCodeVerifier');
            if (empty($authCodeVerifier)) {
                // Prevent PKCE Downgrade Attack
                // https://datatracker.ietf.org/doc/html/draft-ietf-oauth-security-topics#name-pkce-downgrade-attack
                throw new HttpException(409, 'Invalid auth code verifier.');
            }
            $defaultParams['code_verifier'] = $authCodeVerifier;
            $this->removeState('authCodeVerifier');
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        // Azure AD will complain if there is no `Origin` header.
        if ($this->enablePkce) {
            $request->addHeaders(['Origin' => Url::to('/')]);
        }

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);


        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        //Yii::warning($token, 'token');
        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($_ENV['RSAA_USERINFO_URL'])
            ->setHeaders([
                'Authorization' => 'Bearer ' . $token->getToken(),
                'Content-Type' => 'application/json', // Example header
            ]);
        $response = $this->sendRequest($request);
        Yii::warning($response, 'есть контакт');

        $this->roles = $response['roles']??null;
        return $token;
    }

    protected function defaultName()
    {
        return 'rsaa';
    }

    protected function defaultTitle()
    {
        return 'RSAA';
    }


    protected function initUserAttributes()
    {
        return $this->api('info', 'GET');
    }



    /**
     * {@inheritdoc}
     * @throws InvalidConfigException
     */
    public function applyAccessTokenToRequest($request, $accessToken)
    {
        Yii::warning('applyAccessTokenToRequest', 'rsaa ');
        switch($this->accessTokenLocation) {
            case self::ACCESS_TOKEN_LOCATION_BODY:
                $data = $request->getData();
                $data['access_token'] = $accessToken->getToken();
                $request->setData($data);
                break;
            case self::ACCESS_TOKEN_LOCATION_HEADER:
                $request->getHeaders()->set('Authorization', 'Bearer ' . $accessToken->getToken());
                break;
            default:
                throw new InvalidConfigException('Unknown access token location: ' . $this->accessTokenLocation);
        }
    }

    /**
     * Applies client credentials (e.g. [[clientId]] and [[clientSecret]]) to the HTTP request instance.
     * This method should be invoked before sending any HTTP request, which requires client credentials.
     * @param \yii\httpclient\Request $request HTTP request instance.
     * @since 2.1.3
     */
    protected function applyClientCredentialsToRequest($request)
    {
        Yii::warning('applyClientCredentialsToRequest', 'rsaa ');
        $request->addData([
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ]);
        Yii::warning($request, 'rsaa data ');
    }

    /**
     * Gets new auth token to replace expired one.
     * @param OAuthToken $token expired auth token.
     * @return OAuthToken new auth token.
     */
    public function refreshAccessToken(OAuthToken $token)
    {
        Yii::warning('refreshAccessToken', 'rsaa ');

        $params = [
            'grant_type' => 'refresh_token'
        ];
        $params = array_merge($token->getParams(), $params);

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData($params);

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Generates the auth state value.
     * @return string auth state value.
     * @since 2.1
     */
    protected function generateAuthState()
    {
        Yii::warning('generateAuthState', 'rsaa ');
        $baseString = get_class($this) . '-' . time();
        if (Yii::$app->has('session')) {
            $baseString .= '-' . Yii::$app->session->getId();
        }
        return hash('sha256', uniqid($baseString, true));
    }

    /**
     * Creates token from its configuration.
     * @param array $tokenConfig token configuration.
     * @return OAuthToken token instance.
     */
    protected function createToken(array $tokenConfig = [])
    {
        Yii::warning('createToken', 'rsaa ');
        $defaultTokenConfig = ['tokenParamKey' => 'access_token'];
        $tokenConfig = array_merge($defaultTokenConfig, $tokenConfig);

        return parent::createToken($tokenConfig);
    }

    /**
     * Authenticate OAuth client directly at the provider without third party (user) involved,
     * using 'client_credentials' grant type.
     * @see https://tools.ietf.org/html/rfc6749#section-4.4
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @since 2.1.0
     */
    public function authenticateClient($params = [])
    {
        Yii::warning('authenticateClient', 'rsaa ');
        $defaultParams = [
            'grant_type' => 'client_credentials',
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }

    /**
     * Authenticates user directly by 'username/password' pair, using 'password' grant type.
     * @see https://tools.ietf.org/html/rfc6749#section-4.3
     * @param string $username user name.
     * @param string $password user password.
     * @param array $params additional request params.
     * @return OAuthToken access token.
     * @since 2.1.0
     */
    public function authenticateUser($username, $password, $params = [])
    {
        Yii::warning('authenticateUser', 'rsaa ');
        $defaultParams = [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ];

        if (!empty($this->scope)) {
            $defaultParams['scope'] = $this->scope;
        }

        $request = $this->createRequest()
            ->setMethod('POST')
            ->setUrl($this->tokenUrl)
            ->setData(array_merge($defaultParams, $params));

        $this->applyClientCredentialsToRequest($request);

        $response = $this->sendRequest($request);

        $token = $this->createToken(['params' => $response]);
        $this->setAccessToken($token);

        return $token;
    }




}