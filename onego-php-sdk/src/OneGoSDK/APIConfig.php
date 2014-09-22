<?php

class OneGoSDK_APIConfig implements OneGoSDK_Interface_Config
{
    public $apiUri;
    public $apiKey;
    public $apiSecret;
    public $terminalId;
    public $transactionTtl;
    public $transactionTtlAutoRenew;
    public $connectionTimeout;

    /**
     * Initialize API configuration
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $terminalId
     * @param int $transactionTtl
     * @param bool $ttlAutoRenew
     * @param int $connectionTimeout
     */
    public function __construct($apiKey, $apiSecret, $terminalId, 
            $apiUri = 'https://api.onego.com/pos/v1/',
            $transactionTtl = 900, $ttlAutoRenew = true, $connectionTimeout = 0)
    {
        $this->apiKey                   = $apiKey;
        $this->apiSecret                = $apiSecret;
        $this->terminalId               = $terminalId;
        $this->apiUri                   = $apiUri;
        $this->transactionTtl           = $transactionTtl;
        $this->transactionTtlAutoRenew  = $ttlAutoRenew;
        $this->connectionTimeout        = $connectionTimeout;
    }
}