<?php
class OneGoSDK_Impl_SimpleOAuth implements OneGoSDK_Interface_SimpleOAuth
{
    protected $oauth;
    
    /**
     *
     * @param OneGoSDK_Impl_OneGoOAuth $oauth 
     */
    public function __construct(OneGoSDK_Impl_OneGoOAuth $oauth) 
    {
        $this->oauth = $oauth;
    }
    
    /**
     * Simple initializer
     *
     * @param OneGoSDK_OAuthConfig $config
     * @param OneGoSDK_Impl_CurlHttpClient $httpClient
     * @return OneGoSDK_Impl_SimpleOAuth
     */
    public static function init(OneGoSDK_OAuthConfig $config, $httpClient = false)
    {
        if (!$httpClient) {
            $httpClient = new OneGoSDK_Impl_CurlHttpClient();
        }
        $gateway = new OneGoSDK_Impl_OAuthGateway($config, $httpClient);
        return new self(new OneGoSDK_Impl_OneGoOAuth($gateway));
    }
    
    /**
     *
     * @return OneGoSDK_OAuthConfig 
     */
    protected function getConfig()
    {
        return $this->api->getConfig();
    }
    
    /**
     *
     * @param string $redirectUri
     * @param array $scopes
     * @param string $state
     * @param boolean $autologin
     * @return string URI for OAuth authorization code request
     */
    public function getAuthorizationUrl($redirectUri, $scopes = array(), 
        $state = null, $autologin = null)
    {
        if (!empty($scopes) && !is_array($scopes)) {
            $scopes = array($scopes);
        }
        
        $queryParams = array(
            'client_id'     => $this->oauth->getConfig()->apiKey,
            'response_type' => 'code',
            'redirect_uri'  => $redirectUri,
            'scope'         => !empty($scopes) ? implode(' ', $scopes) : null,
            'state'         => $state,
            'autologin'     => $autologin ? 'true' : null,
        );
        $url = $this->oauth->getConfig()->getOAuthBaseUri().'/authorize';
        foreach ($queryParams as $key => $val) {
            if (!empty($val)) {
                $prefix = strpos($url, '?') ? '&' : '?';
                $url .= $prefix.$key.'='.urlencode($val);
            }
        }
        return $url;
    }
    
    /**
     * Request OAuth token
     *
     * @param string $authorizationCode
     * @param string $redirectUri
     * @return OneGoSDK_Impl_OAuthToken 
     */
    public function requestAccessToken($authorizationCode, $redirectUri)
    {
        $request = new OneGoSDK_DTO_OAuthTokenRequestDto();
        $request->grant_type = 'authorization_code';
        $request->code = $authorizationCode;
        $request->redirect_uri = $redirectUri;
        try {
            $tokenDto = $this->oauth->requestAccessToken($request);
            return OneGoSDK_Impl_OAuthToken::createByDto($tokenDto);
        } catch (OneGoSDK_Exception $e) {
            throw $e;
        }
    }
    
    /**
     *
     * @param string $redemptionCode
     * @param string $redirectUri
     * @return OneGoSDK_Impl_OAuthToken 
     */
    public function requestAccessTokenByRedemptionCode($redemptionCode, $redirectUri)
    {
        $request = new OneGoSDK_DTO_OAuthTokenByRedemptionCodeRequestDto();
        $request->grant_type = 'redemption_code';
        $request->number = $redemptionCode;
        $request->redirect_uri = $redirectUri;
        try {
            $tokenDto = $this->oauth->requestAccessTokenByRedemptionCode($request);
            return OneGoSDK_Impl_OAuthToken::createByDto($tokenDto);
        } catch (OneGoSDK_Exception $e) {
            throw $e;
        }
    }
    
    /**
     *
     * @param string $refreshToken
     * @return OneGoSDK_Impl_OAuthToken 
     */
    public function refreshAccessToken($refreshToken)
    {
        $request = new OneGoSDK_DTO_OAuthTokenRefreshRequestDto();
        $request->grant_type = 'refresh_token';
        $request->refresh_token = $refreshToken;
        try {
            $tokenDto = $this->oauth->refreshAccessToken($request);
            return OneGoSDK_Impl_OAuthToken::createByDto($tokenDto);
        } catch (OneGoSDK_Exception $e) {
            throw $e;
        }
    }
    
    /**
     *
     * @param string $accessToken
     * @return array
     */
    public function checkAccessToken($accessToken)
    {
        try {
            $tokenDto = $this->oauth->checkAccessToken($accessToken);
            return OneGoSDK_Impl_OAuthToken::createByDto($tokenDto);
        } catch (OneGoSDK_Exception $e) {
            throw $e;
        }
    }

    /**
     *
     * @param string $accessToken
     * @return array
     */
    public function revokeAccessToken($accessToken)
    {
        return $this->revokeToken($accessToken, OneGoSDK_Impl_OneGoOAuth::TOKEN_TYPE_ACCESS);
    }

    /**
     *
     * @param string $refreshToken
     * @return array
     */
    public function revokeRefreshToken($refreshToken)
    {
        return $this->revokeToken($refreshToken, OneGoSDK_Impl_OneGoOAuth::TOKEN_TYPE_REFRESH);
    }

    public function revokeToken($token, $type)
    {
        try {
            $request = new OneGoSDK_DTO_OAuthRevokeTokenRequestDto();
            $request->type = $type;
            return $this->oauth->revokeToken($token, $request);
        } catch (OneGoSDK_Exception $e) {
            throw $e;
        }
    }
}