<?php

class BaseUnitTestCase extends UnitTestCase
{
    /**
     * @return OneGoSDK_APIConfig
     */
    protected
    function getApiConfig()
    {
        return new OneGoSDK_APIConfig('apikey', 'apisecret', 'php-tests', 'http://api.uri', 600, true, 3);
    }
    
    /**
     * @return OneGoSDK_OAuthConfig
     */
    protected
    function getOAuthConfig()
    {
        return new OneGoSDK_OAuthConfig('apikey', 'apisecret', 'http://auth.request.uri', 3);
    }
}