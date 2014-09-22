<?php

class OneGoSDK_Impl_Gateway implements OneGoSDK_Interface_Gateway
{
    const MEDIA_APPLICATION_JSON = 'application/json';
    const MEDIA_APPLICATION_X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';

    protected $config;
    protected $httpClient;
    protected $authorizationHeader;

    public function __construct(OneGoSDK_Interface_Config $config,
            OneGoSDK_Interface_HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
    }

    /**
     *
     * @return OneGoSDK_Interface_Config 
     */
    public function getConfig()
    {
        return $this->config;
    }
    
    /**
     * POST request
     *
     * @param string $resource
     * @param array $request
     * @return stdClass
     * @throws OneGoSDK_Exception 
     */
    protected function post($resource, $request = array())
    {
        $url    = $this->url($resource);
        $input  = OneGoSDK_JSON::encode($request);

        OneGoSDK_Log::debug('Calling API (%s) with POST request=%s', $url, $input);
        
        $headers = array(
            'Content-Type: ' . self::MEDIA_APPLICATION_JSON,
            'Accept: ' . self::MEDIA_APPLICATION_JSON,
        );
        if (!empty($this->authorizationHeader)) {
            array_unshift($headers, $this->authorizationHeader);
        }
        
        $response = $this->httpClient->request($url, $input, $headers, OneGoSDK_Interface_HttpClient::METHOD_POST);
        
        try {
            $result = $this->processResponse($response);
        } catch (Exception $e) {
            OneGoSDK_Log::warn('API response=%s', $response);
            throw $e;
        }

        OneGoSDK_Log::debug('API response=%s', $response);
        
        return $result;
    }
    
    /**
     * GET request
     *
     * @param string $resource
     * @param array $request
     * @return stdClass
     * @throws OneGoSDK_Exception 
     */
    protected function get($resource, $request = array())
    {
        $url = $this->url($resource);
        
        OneGoSDK_Log::debug('Calling API (%s) with GET request=%s', $url, $request);
        
        $headers = array(
            'Content-Type: ' . self::MEDIA_APPLICATION_JSON,
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
            OneGoSDK_Log::warn('API response=%s', $response);
            throw $e;
        }
        
        OneGoSDK_Log::debug('API response=%s', $response);

        return $result;
    }
    
    /**
     * Check gateway request response, throw exception if HTTP status code signals error
     * and response is not a valid OneGoSDK_DTO_ErrorDto
     *
     * @param string $response
     * @return stdClass decoded response
     * @throws OneGoSDK_Exception
     */
    protected function processResponse($response)
    {
        if ($response === false) {
            throw new OneGoSDK_HTTPConnectionTimeoutException($this->httpClient->getResponseError());
        } else if (!$result = OneGoSDK_JSON::decode($response)) {
            $error = $this->resolveExceptionFromHttpStatus($this->httpClient->getResponseStatus(), $response);
            if ($error) {
                throw $error;
            } else {
                throw new OneGoSDK_Exception("Couldn't unserialize response: $response");
            }
        }
        
        if ($this->httpClient->getResponseStatus() != 200) {
            $error = $this->resolveExceptionFromHttpStatus($this->httpClient->getResponseStatus(), $result);
            if ($error) {
                throw $error;
            }
        }
        
        return $result;
    }
    
    /**
     *
     * @param array $request
     * @return string array converted into query string 
     */
    protected function arrayToQueryString($request)
    {
        $arr = array();
        foreach ($request as $key => $val) {
            $arr[] = $key.'='.urlencode($val);
        }
        return implode('&', $arr);
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
                $error = OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_ErrorDto', $responseData);
                return false;
            } catch (Exception $e) {
                
            }
        }
        
        switch ($httpStatus) {
            case '200':
                return false;
            case '204':
                return new OneGoSDK_NoContentException('Request returned HTTP status 204: No content');
            case '400':
                return new OneGoSDK_InvalidInputException('Request returned HTTP status '.$httpStatus.': Bad Request');
            case '401':
                return new OneGoSDK_UnauthorizedException('Request returned HTTP status '.$httpStatus.': Unauthorized');
            case '403':
                return new OneGoSDK_BlockedException('Request returned HTTP status '.$httpStatus.': Forbidden');
            default:
                return new OneGoSDK_Exception('Request returned HTTP status '.$httpStatus);
        }
    }
    
    protected function setHttpBasicAuthorization($user, $password)
    {
        $this->authorizationHeader = $this->httpClient->authBasic($user, $password);
    }
}