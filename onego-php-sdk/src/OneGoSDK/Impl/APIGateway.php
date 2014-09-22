<?php

class OneGoSDK_Impl_APIGateway extends OneGoSDK_Impl_Gateway
{
    public function __construct(OneGoSDK_APIConfig $config,
            OneGoSDK_Interface_HttpClient $httpClient)
    {
        $this->config = $config;
        $this->httpClient = $httpClient;
        $this->httpClient->setConnectionTimeout($config->connectionTimeout);
    }

    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token OAuth token
     * @param OneGoSDK_DTO_TransactionBeginRequestDto $request API call request DTO
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function beginTransaction(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_TransactionBeginRequestDto $request) 
    {
        $this->setAuthorizationToken($token);
        return $this->post('transaction/begin', $request);
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_TransactionEndDto $request Request DTO
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function endTransaction(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_TransactionEndDto $request)
    {   
        $this->setAuthorizationToken($token);
        return $this->post('transaction/end', $request);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_TransactionEndDto $request Request DTO
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function endTransactionBasic(OneGoSDK_DTO_TransactionEndDto $request) {   
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->post('transaction/end', $request);
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_TransactionCartDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function updateCart(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_TransactionCartDto $request)
    {
        $this->setAuthorizationToken($token);
        return $this->post('transaction', $request);
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_FundsOperationDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function spendPrepaid(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_FundsOperationDto $request)
    {
        $this->setAuthorizationToken($token);
        return $this->post('transaction/prepaid/spend', $request);
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_FundsOperationCancelDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function cancelSpendingPrepaid(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_FundsOperationCancelDto $request)
    {
        $this->setAuthorizationToken($token);
        return $this->post('transaction/prepaid/spending/cancel', $request);
    }
        
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_TransactionIdDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function getTransaction(
        OneGoSDK_Interface_OAuthToken $token,
        OneGoSDK_DTO_TransactionIdDto $request)
    {
        $this->setAuthorizationToken($token);
        return $this->get('transaction/'.$request->id.'/'.$request->type);
    }

    /**
     *
     * @param OneGoSDK_DTO_TransactionIdDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function getTransactionBasic(OneGoSDK_DTO_TransactionIdDto $request)
    {
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->get('transaction/'.$request->id.'/'.$request->type);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_CalculateAwardsDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function awards(OneGoSDK_DTO_CalculateAwardsDto $request)
    {
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->post('awards', $request);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_TransactionBindNewRequestDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function bindNew(OneGoSDK_DTO_TransactionBindNewRequestDto $request)
    {
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->post('transaction/bind/new', $request);
    }
    
    /**
     *
     * @param OneGoSDK_DTO_TransactionBindRequestDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function bind(OneGoSDK_DTO_TransactionBindRequestDto $request)
    {
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->post('transaction/bind', $request);
    }
    
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token
     * @param OneGoSDK_DTO_UseRedemptionCodeRequestDto $request
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function useRedemptionCode(
            OneGoSDK_Interface_OAuthToken $token,
            OneGoSDK_DTO_UseRedemptionCodeRequestDto $request)
    {
        $this->setAuthorizationToken($token);
        return $this->post('transaction/redemption-code/use', $request);
    }

    /**
     *
     * @return stdClass API call response object
     * @throws OneGoSDK_Exception
     */
    public function me()
    {
        $this->setHttpBasicAuthorization($this->getConfig()->apiKey, $this->getConfig()->apiSecret);
        return $this->get('me');
    }
    
    /**
     *
     * @param string $resource
     * @return string API call URI for resource
     */
    protected function url($resource)
    {
        return rtrim($this->config->apiUri, '/') . '/' . $resource;
    }
    
    /**
     * Set OAuth token to be used in API calls
     *
     * @param OneGoSDK_Interface_OAuthToken $token 
     */
    protected function setAuthorizationToken(OneGoSDK_Interface_OAuthToken $token)
    {
        $this->authorizationHeader = $token->getAuthorizationHeader();
    }
}