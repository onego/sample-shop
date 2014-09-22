<?php

Mock::generate('OneGoSDK_Impl_CurlHttpClient');
Mock::generate('OneGoSDK_Impl_OAuthToken');

class ApiGatewayTest extends BaseUnitTestCase
{
    private $unit;

    private $httpClient;

    private $config;
    
    private $token;
    
    private $successfullResponse;

    public
    function setUp()
    {
        $this->config           = $this->getApiConfig();
        $this->config->apiUri   = 'http://localhost/' . microtime(true);

        $this->httpClient       = new MockOneGoSDK_Impl_CurlHttpClient();
        
        $this->unit             = new OneGoSDK_Impl_ApiGateway(
            $this->config,
            $this->httpClient
        );
        
        $this->token = new MockOneGoSDK_Impl_OAuthToken();
        $this->token->setReturnValue(
            'getAuthorizationHeader', 
            'Authorization: Bearer OAUTHACCESSTOKEN'
        );
        
        $this->successfullResponse = new stdClass();
        $this->successfullResponse->response = 'success';
    }
    
    protected function setResponseSuccessful()
    {
        $this->httpClient->setReturnValue('getResponseStatus', 200);
        $this->httpClient->setReturnValue('request', '{"response": "success"}');
    }
    
    public function testRequireTokenAuthorizationHeader()
    {
        $this->setResponseSuccessful();
        $this->token->expectOnce('getAuthorizationHeader');
        $this->httpClient->expectOnce('request', array(
            '*',
            '*',
            array(
                'Authorization: Bearer OAUTHACCESSTOKEN',
                'Content-Type: application/json',
                'Accept: application/json',
            ),
            '*'
        ));
        $response = $this->unit->beginTransaction($this->token, new OneGoSDK_DTO_TransactionBeginRequestDto());
    }

    public function testBeginTransaction()
    {
        $req = new OneGoSDK_DTO_TransactionBeginRequestDto();
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/begin',
            OneGoSDK_JSON::encode($req),
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->beginTransaction($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testBeginTransactionFail()
    {
        $this->httpClient->setReturnValue('getResponseStatus', 404);
        $this->expectException('OneGoSDK_Exception');
        $response = $this->unit->beginTransaction($this->token, new OneGoSDK_DTO_TransactionBeginRequestDto());
    }
    
    public function testBeginTransactionResponseError()
    {
        $this->httpClient->setReturnValue('request', '');
        $this->expectException('OneGoSDK_Exception');
        $response = $this->unit->beginTransaction($this->token, new OneGoSDK_DTO_TransactionBeginRequestDto());
    }
    
    public function testEndTransaction()
    {
        $req = new OneGoSDK_DTO_TransactionEndDto();
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/end',
            OneGoSDK_JSON::encode($req),
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->endTransaction($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testGetTransaction()
    {
        $req = new OneGoSDK_DTO_TransactionIdDto();
        $req->id = 'ID';
        $req->type = 'TYPE';
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/ID/TYPE',
            null,
            '*',
            'GET'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->getTransaction($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }

    public function testGetTransactionBasic()
    {
        $req = new OneGoSDK_DTO_TransactionIdDto();
        $req->id = 'ID';
        $req->type = 'TYPE';

        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/ID/TYPE',
            null,
            '*',
            'GET'
        ));
        $this->setResponseSuccessful();

        $response = $this->unit->getTransactionBasic($req);

        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testUpdateCart()
    {
        $req = new OneGoSDK_DTO_TransactionCartDto();
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction',
            OneGoSDK_JSON::encode($req),
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->updateCart($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testSpendPrepaid()
    {
        $req = new OneGoSDK_DTO_FundsOperationDto();
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/prepaid/spend',
            OneGoSDK_JSON::encode($req),
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->spendPrepaid($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testCancelSpendingPrepaid()
    {
        $req = new OneGoSDK_DTO_FundsOperationCancelDto();
        
        $this->httpClient->expectOnce('request', array(
            $this->config->apiUri . '/transaction/prepaid/spending/cancel',
            OneGoSDK_JSON::encode($req),
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->cancelSpendingPrepaid($this->token, $req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
}