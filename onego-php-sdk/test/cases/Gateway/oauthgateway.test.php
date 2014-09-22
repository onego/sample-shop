<?php

Mock::generate('OneGoSDK_Impl_CurlHttpClient');
Mock::generate('OneGoSDK_Impl_OAuthToken');

class OAuthGatewayTest extends BaseUnitTestCase
{
    private $unit;

    private $httpClient;

    private $config;
    
    private $token;
    
    private $successfullResponse;

    public
    function setUp()
    {
        $this->config           = $this->getOAuthConfig();

        $this->httpClient       = new MockOneGoSDK_Impl_CurlHttpClient();
        
        $this->successfullResponse = new stdClass();
        $this->successfullResponse->response = 'success';
        
        $this->httpClient->expectOnce('authBasic', array(
            $this->config->apiKey,
            $this->config->apiSecret
        ));
        $this->httpClient->setReturnValue(
            'authBasic',
            'Authorization: Basic OTOTORIZATION'
        );
        
        $this->unit             = new OneGoSDK_Impl_OAuthGateway(
            $this->config,
            $this->httpClient
        );
    }
    
    protected function setResponseSuccessful()
    {
        $this->httpClient->setReturnValue('getResponseStatus', 200);
        $this->httpClient->setReturnValue('request', '{"response": "success"}');
    }
    
    public function testRequestHeaders()
    {
        $this->httpClient->expectOnce('request', array(
            '*',
            '*',
            array(
                'Authorization: Basic OTOTORIZATION',
                'Content-Type: application/x-www-form-urlencoded',
                'Accept: application/json',
            ),
            '*'
        ));
        $this->setResponseSuccessful();
        $response = $this->unit->requestAccessToken(new OneGoSDK_DTO_OAuthTokenRequestDto());
    }

    public function testRequestAccessToken()
    {
        $req = new OneGoSDK_DTO_OAuthTokenRequestDto();
        $req->code = '123';
        $req->grant_type = 'code';
        $req->redirect_uri = 'someuri';
        
        $this->httpClient->expectOnce('request', array(
            $this->config->oAuthBaseUri . '/token',
            'code=123&grant_type=code&redirect_uri=someuri',
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->requestAccessToken($req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testRefreshAccessToken()
    {
        $req = new OneGoSDK_DTO_OAuthTokenRefreshRequestDto();
        $req->refresh_token = '123';
        $req->grant_type = 'refresh_token';
        
        $this->httpClient->expectOnce('request', array(
            $this->config->oAuthBaseUri . '/token',
            'refresh_token=123&grant_type=refresh_token&scope=',
            '*',
            'POST'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->refreshAccessToken($req);
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testCheckAccessToken()
    {
        $this->httpClient->expectOnce('request', array(
            $this->config->oAuthBaseUri . '/token/TOKEN',
            '*',
            '*',
            'GET'
        ));
        $this->setResponseSuccessful();
        
        $response = $this->unit->checkAccessToken('TOKEN');
        
        $this->assertEqual($this->successfullResponse, $response);
    }
    
    public function testRequestFail()
    {
        $this->httpClient->setReturnValue('getResponseStatus', 404);
        $this->expectException('OneGoSDK_Exception');
        $response = $this->unit->requestAccessToken(new OneGoSDK_DTO_OAuthTokenRequestDto());
    }
    
    public function testResponseError()
    {
        $this->httpClient->setReturnValue('request', '');
        $this->expectException('OneGoSDK_Exception');
        $response = $this->unit->requestAccessToken(new OneGoSDK_DTO_OAuthTokenRequestDto());
    }
}