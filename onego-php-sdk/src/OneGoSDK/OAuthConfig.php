<?php
class OneGoSDK_OAuthConfig implements OneGoSDK_Interface_Config
{
    public $apiKey;
    public $apiSecret;
    public $oAuthBaseUri;
    public $connectionTimeout;

    /**
     * Initialize OAuth config
     *
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $oAuthBaseUri
     * @param int $connectionTimeout
     */
    public function __construct($apiKey, $apiSecret, 
            $oAuthBaseUri = 'https://auth.onego.com/oauth2/',
            $connectionTimeout = 0)
    {
        $this->apiKey               = $apiKey;
        $this->apiSecret            = $apiSecret;
        $this->oAuthBaseUri         = $oAuthBaseUri;
        $this->connectionTimeout    = $connectionTimeout;
    }

    public function getOAuthBaseUri()
    {
        return rtrim($this->oAuthBaseUri, '/');
    }
}