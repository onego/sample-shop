<?php
class OneGoSDK_Impl_SimpleAPI implements OneGoSDK_Interface_SimpleAPI 
{
    protected $api;
    protected $transaction;
    
    /**
     *
     * @param OneGoSDK_Impl_OneGoAPI $api 
     */
    public function __construct(OneGoSDK_Impl_OneGoAPI $api) 
    {
        $this->api = $api;
    }
    
    /**
     * Simple initializator
     *
     * @param OneGoSDK_APIConfig $config
     * @param OneGoSDK_Impl_CurlHttpClient $httpClient
     * @return OneGoSDK_Impl_SimpleAPI
     */
    public static function init(OneGoSDK_APIConfig $config, $httpClient = false)
    {
        if (!$httpClient) {
            $httpClient = new OneGoSDK_Impl_CurlHttpClient();
        }
        $gateway = new OneGoSDK_Impl_APIGateway($config, $httpClient);
        $api = new OneGoSDK_Impl_OneGoAPI($gateway);
        $simpleapi = new self($api);
        return $simpleapi;
    }
    
    /**
     * Store and bind separate transaction object with OneGoAPI object
     *
     * @param OneGoSDK_Interface_Transaction $transaction 
     */
    public function setTransaction(OneGoSDK_Interface_Transaction $transaction)
    {
        $transaction->setApi($this->api);
        $this->transaction = $transaction;
    }
    
    /**
     * Get stored transaction object
     *
     * @return OneGoSDK_Interface_Transaction 
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
    
    /**
     * Set OAuth token
     *
     * @param OneGoSDK_Interface_OAuthToken $token 
     */
    public function setOAuthToken(OneGoSDK_Interface_OAuthToken $token)
    {
        $this->api->setOAuthToken($token);
    }
    
    /**
     *
     * @return OneGoSDK_APIConfig 
     */
    protected function getConfig()
    {
        return $this->api->getConfig();
    }
    
    /**
     *
     * @return boolean True if transaction is started
     */
    public function isTransactionStarted()
    {
        return $this->getTransaction() && $this->getTransaction()->isStarted();
    }
    
    /**
     *
     * @return boolean True if transaction is delayed
     */
    public function isTransactionDelayed()
    {
        return $this->getTransaction() && $this->getTransaction()->isDelayed();
    }
    
    /**
     *
     * @param string $receiptNumber
     * @param OneGoSDK_Impl_Cart $initialCart
     * @param string $externalId
     * @param integer $transactionTtl
     * @return OneGoSDK_Impl_Transaction
     */
    public function beginTransaction($receiptNumber, OneGoSDK_Impl_Cart $initialCart = null, $externalId = null, $transactionTtl = null)
    {
        if ($this->isTransactionStarted()) {
            throw new OneGoSDK_Exception('Transaction already started');
        }
        
        $req = new OneGoSDK_DTO_TransactionBeginRequestDto();
        $req->receiptNumber = $receiptNumber;
        $req->bimId = null;
        $req->terminalId = $this->getConfig()->terminalId;
        $req->ttl = !is_null($transactionTtl) ? $transactionTtl : $this->getConfig()->transactionTtl;
        $req->ttlAutoRenew = $this->getConfig()->transactionTtlAutoRenew;
        if (!empty($initialCart)) {
            $req->cartEntries = array_values($initialCart->getEntries());
        }
        if (!empty($externalId)) {
            $req->externalId = $externalId;
        }
        try {
            $dto = $this->api->beginTransaction($req);
            $transaction = new OneGoSDK_Impl_Transaction($dto, $req->ttl, $req->ttlAutoRenew);
            $this->setTransaction($transaction);
        } catch (Exception $e) {
            throw $e;
        }
        return $this->getTransaction();
    }
    
    /**
     * Apply awards to anonymous buyer's cart (detailed or with $purchaseSum specified only)
     *
     * @param OneGoSDK_Impl_Cart $cart
     * @param float $purchaseSum
     * @return OneGoSDK_DTO_ModifiedCartDto 
     */
    public function getAnonymousAwards(OneGoSDK_Impl_Cart $cart = null, $purchaseSum = null)
    {
        $req = new OneGoSDK_DTO_CalculateAwardsDto();
        $req->terminalId = $this->getConfig()->terminalId;
        if (!empty($cart)) {
            $req->cartEntries = array_values($cart->getEntries());
        } else if (!empty($purchaseSum)) {
            $req->purchaseSum = $purchaseSum;
        }
        
        return $this->api->awards($req);
    }
    
    /**
     *
     * @param string $email
     * @param string $receiptNumber
     * @param mixed $delayTtl Integer to create delayed transaction (delay in seconds), 
     *              or false to create confirmed transaction
     * @param OneGoSDK_Impl_Cart $cart
     * @param float $purchaseSum
     * @return OneGoSDK_Impl_Transaction 
     */
    public function bindEmailNew($email, $receiptNumber, $delayTtl = false, OneGoSDK_Impl_Cart $cart = null, $purchaseSum = null)
    {
        $req = new OneGoSDK_DTO_TransactionBindNewRequestDto();
        $req->terminalId = $this->getConfig()->terminalId;
        $req->receiptNumber = $receiptNumber;
        $req->delay = (int) $delayTtl ? (int) $delayTtl : null;
        $req->email = trim($email);
        if (!empty($cart)) {
            $req->cartEntries = array_values($cart->getEntries());
        } else if (!empty($purchaseSum)) {
            $req->purchaseSum = $purchaseSum;
        }
        
        $transactionDto = $this->api->bindNew($req);
        $transaction = new OneGoSDK_Impl_Transaction($transactionDto, $req->delay, false);
        
        if ($req->delay) {
            // transaction is delayed
            $transaction->setDelayedState($req->delay);
        }
        $transaction->setEndedState();
        $this->setTransaction($transaction);
        
        return $this->getTransaction();
    }

    /**
     *
     * @param string $sessionToken
     * @param string $receiptNumber
     * @param mixed $delayTtl Integer to create delayed transaction (delay in seconds),
     *              or false to create confirmed transaction
     * @param OneGoSDK_Impl_Cart $cart
     * @param float $purchaseSum
     * @return OneGoSDK_Impl_Transaction
     */
    public function bindSessionTokenNew($sessionToken, $receiptNumber, $delayTtl = false, OneGoSDK_Impl_Cart $cart = null, $purchaseSum = null)
    {
        $req = new OneGoSDK_DTO_TransactionBindNewRequestDto();
        $req->terminalId = $this->getConfig()->terminalId;
        $req->receiptNumber = $receiptNumber;
        $req->delay = (int) $delayTtl ? (int) $delayTtl : null;
        $req->sessionToken = $sessionToken;
        if (!empty($cart)) {
            $req->cartEntries = array_values($cart->getEntries());
        } else if (!empty($purchaseSum)) {
            $req->purchaseSum = $purchaseSum;
        }

        $transactionDto = $this->api->bindNew($req);
        $transaction = new OneGoSDK_Impl_Transaction($transactionDto, $req->delay, false);

        if ($req->delay) {
            // transaction is delayed
            $transaction->setDelayedState($req->delay);
        }
        $transaction->setEndedState();
        $this->setTransaction($transaction);

        return $this->getTransaction();
    }
    
    /**
     *
     * @return OneGoSDK_Impl_Cart Empty cart object
     */
    public function newCart()
    {
        return new OneGoSDK_Impl_Cart();
    }
    
    public function fetchTransactionById($transactionId, $useHTTPBasicAuth = true)
    {
        $req = new OneGoSDK_DTO_TransactionIdDto();
        $req->type = OneGoSDK_DTO_TransactionIdDto::TYPE_ONEGO;
        $req->id = $transactionId;
        if ($useHTTPBasicAuth) {
            $dto = $this->api->getTransactionBasic($req);
        } else {
            $dto = $this->api->getTransaction($req);
        }
        $transaction = new OneGoSDK_Impl_Transaction($dto, 
                $this->getConfig()->transactionTtl, $this->getConfig()->transactionTtlAutoRenew);
        $this->setTransaction($transaction);
        return $transaction;
    }
}