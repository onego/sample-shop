<?php

class OneGoSDK_Impl_CurlHttpClient
        implements OneGoSDK_Interface_HttpClient
{
    protected $_response_details;
    protected $_response_error;
    protected $_connectionTimeout = 0;

    /**
     *
     * @param integer $connectionTimeout Timeout in seconds
     */
    public function setConnectionTimeout($connectionTimeout) {
        $this->_connectionTimeout = $connectionTimeout;
    }

    /**
     *
     * @return mixed HTTP status or false
     */
    public function getResponseStatus()
    {
        return !empty($this->_response_details['http_code']) ?
                $this->_response_details['http_code'] : false;
    }

    /**
     *
     * @return array curl_getinfo() data
     */
    public function getResponseDetails()
    {
        return $this->_response_details;
    }

    protected function setResponseDetails($details)
    {
        $this->_response_details = $details;
    }

    /**
     *
     * @return string CURL request error
     */
    public function getResponseError()
    {
        return $this->_response_error;
    }

    protected function setResponseError($error)
    {
        $this->_response_error = $error;
    }

    /**
     *
     * @param string $user
     * @param string $password
     * @return string HTTP basic authorization header string
     */
    public function authBasic($user, $password)
    {
        return 'Authorization: Basic ' . base64_encode("$user:$password");
    }

    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @return string HTTP authorization header string for OAuth token
     */
    public function authOAuthToken(OneGoSDK_Interface_OAuthToken $token)
    {
        return $token->getAuthorizationHeader();
    }

    /**
     *
     * @param string $url
     * @param array $data
     * @param array $headers
     * @param string $method Request method
     * @return string request result
     */
    public function request($url, $data = NULL, $headers = array(), $method = self::METHOD_GET)
    {
        $this->_response_status = false;

        $ch = curl_init($url);

        if ($method == self::METHOD_POST) {
            curl_setopt($ch, CURLOPT_POST, true);
        } else if ($method != self::METHOD_GET) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        }

        if (!is_null($data) && $method != self::METHOD_GET)
        {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->_connectionTimeout);

        $res = curl_exec($ch);

        $this->setResponseDetails(curl_getinfo($ch));
        $this->setResponseError(curl_error($ch));

        return $res;
    }
}
