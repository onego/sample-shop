<?php

class OneGoSDK_Impl_OAuthGateway extends OneGoSDK_Impl_Gateway
{
    public function __construct(OneGoSDK_OAuthConfig $config,
            OneGoSDK_Interface_HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->setHttpBasicAuthorization($config->apiKey, $config->apiSecret);
        $this->httpClient->setConnectionTimeout($config->connectionTimeout);
    }

    /**
     *
     * @return OneGoSDK_OAuthConfig 
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     *
     * @param OneGoSDK_DTO_OAuthTokenRequestDto $req
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    public function requestAccessToken(OneGoSDK_DTO_OAuthTokenRequestDto $req) 
    {
        return $this->post('token', $req);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_OAuthTokenRefreshRequestDto $req
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    public function refreshAccessToken(OneGoSDK_DTO_OAuthTokenRefreshRequestDto $req) 
    {
        return $this->post('token', $req);
    }
    
    /**
     *
     * @param string $token OAuth token 
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    public function checkAccessToken($token)
    {
        return $this->get('token/'.$token);
    }

    /**
     *
     * @param string $token OAuth token
     * @param OneGoSDK_DTO_OAuthRevokeTokenRequestDto $req
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException
     */
    public function revokeToken($token, OneGoSDK_DTO_OAuthRevokeTokenRequestDto $req)
    {
        return $this->delete('token/'.$token, $req);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_OAuthTokenByRedemptionCodeRequestDto $req
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    public function requestAccessTokenByRedemptionCode(OneGoSDK_DTO_OAuthTokenByRedemptionCodeRequestDto $req)
    {
        return $this->post('token', $req);
    }
    
    /**
     *
     * @param string $resource
     * @return string API call resource URI 
     */
    protected function url($resource)
    {
        return $this->config->getOAuthBaseUri().'/'.$resource;
    }
    
    /**
     *
     * @param string $resource API resource
     * @param array $request
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    protected function post($resource, $request = array())
    {
        $url = $this->url($resource);
        $input  = OneGoSDK_JSON::encode($request);
        
        OneGoSDK_Log::debug('Calling OAuth (%s) with POST request=%s', $url, $input);
        
        $headers = array(
            'Content-Type: ' . self::MEDIA_APPLICATION_X_WWW_FORM_URLENCODED,
            'Accept: ' . self::MEDIA_APPLICATION_JSON,
        );
        if (!empty($this->authorizationHeader)) {
            array_unshift($headers, $this->authorizationHeader);
        }
        
        // convert request to query string for x-www-form-urlencoded request
        if (is_array($request) || is_object($request)) {
            $request = $this->arrayToQueryString($request);
        }

        $response = $this->httpClient->request($url, $request, $headers, OneGoSDK_Interface_HttpClient::METHOD_POST);

        try {
            $result = $this->processResponse($response);
        } catch (Exception $e) {
            OneGoSDK_Log::warn('OAuth response=%s', $response);
            throw $e;
        }

        OneGoSDK_Log::debug('OAuth response=%s', $response);

        return $result;
    }
    
    /**
     *
     * @param string $resource API resource
     * @param array $request
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException 
     */
    protected function get($resource, $request = array())
    {
        $url = $this->url($resource);
        $input  = OneGoSDK_JSON::encode($request);
        
        OneGoSDK_Log::debug('Calling OAuth (%s) with GET request=%s', $url, $input);
        
        $headers = array(
            'Content-Type: ' . self::MEDIA_APPLICATION_X_WWW_FORM_URLENCODED,
            'Accept: ' . self::MEDIA_APPLICATION_JSON,
        );
        if (!empty($this->authorizationHeader)) {
            array_unshift($headers, $this->authorizationHeader);
        }
        
        // convert request to query string for x-www-form-urlencoded request
        if (is_array($request)) {
            $request = $this->arrayToQueryString($request);
        }
        $request = trim($request);
        if (strlen($request)) {
            $pre = (strpos($url, '?') === false) ? '?' : '&';
            $url .= $pre.$request;
        }

        $response = $this->httpClient->request($url, null, $headers, OneGoSDK_Interface_HttpClient::METHOD_GET);

        try {
            $result = $this->processResponse($response);
        } catch (Exception $e) {
            OneGoSDK_Log::warn('OAuth response=%s', $response);
            throw $e;
        }

        OneGoSDK_Log::debug('OAuth response=%s', $response);

        return $result;
    }

    /**
     *
     * @param string $resource API resource
     * @param array $request
     * @return stdClass API call response object
     * @throws OneGoSDK_OAuthException
     */
    protected function delete($resource, $request = array())
    {
        $url = $this->url($resource);
        $input  = OneGoSDK_JSON::encode($request);

        OneGoSDK_Log::debug('Calling OAuth (%s) with DELETE request=%s', $url, $input);

        $headers = array(
            'Content-Type: ' . self::MEDIA_APPLICATION_X_WWW_FORM_URLENCODED,
            'Accept: ' . self::MEDIA_APPLICATION_JSON,
        );
        if (!empty($this->authorizationHeader)) {
            array_unshift($headers, $this->authorizationHeader);
        }

        // convert request to query string for x-www-form-urlencoded request
        if (is_array($request) || is_object($request)) {
            $request = (array) $request;
        }

        $response = $this->httpClient->request($url, $request, $headers, OneGoSDK_Interface_HttpClient::METHOD_DELETE);

        try {
            $result = $this->processResponse($response);
        } catch (Exception $e) {
            OneGoSDK_Log::warn('OAuth response=%s', $response);
            throw $e;
        }

        OneGoSDK_Log::debug('OAuth response=%s', $response);

        return $result;
    }
    
    /**
     *
     * @param string $httpStatus
     * @param stdClass $responseData
     * @return mixed false if not an error or valid error object returned, 
     *      specific OneGoSDK_Exception if not 
     */
    protected function resolveExceptionFromHttpStatus($httpStatus, $responseData = null)
    {
        // check if valid OneGoSDK_DTO_ErrorDto was returned
        if (is_object($responseData) && !empty($responseData)) {
            try {
                $error = OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_OAuthErrorDto', $responseData);
                return false;
            } catch (Exception $e) {
                
            }
        }
        
        switch ($httpStatus) {
            case '200':
                if ($responseData == '') {
                    return new OneGoSDK_NoContentException('Request returned no content');
                }
                return false;
            default:
                return new OneGoSDK_OAuthException('Request returned HTTP status '.$httpStatus);
        }
    }
}