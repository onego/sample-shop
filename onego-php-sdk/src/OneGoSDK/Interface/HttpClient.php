<?php

interface OneGoSDK_Interface_HttpClient
{
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';

    public function authBasic($user, $password);
    
    public function authOAuthToken(OneGoSDK_Interface_OAuthToken $token);

    public function request($url, $data = NULL, $headers = array(), $method = self::METHOD_GET);
    
    public function setConnectionTimeout($connectionTimeout);
}