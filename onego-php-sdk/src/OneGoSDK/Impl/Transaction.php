<?php
class OneGoSDK_Impl_Transaction implements OneGoSDK_Interface_Transaction 
{
    protected $transactionDto;
    protected $isStarted;
    protected $isDelayed;
    protected $api;
    protected $ttl;
    protected $ttlAutoRenew;
    protected $startedOn;
    protected $lastUpdateOn;
    
    /**
     *
     * @param OneGoSDK_DTO_TransactionDto $dto
     * @param integer $ttl
     * @param boolean $ttlAutoRenew 
     */
    public function __construct(OneGoSDK_DTO_TransactionDto $dto, $ttl, $ttlAutoRenew) 
    {
        $this->transactionDto = $dto;
        $this->ttl = $ttl;
        $this->ttlAutoRenew = $ttlAutoRenew;
        $this->isStarted = true;
        $this->isDelayed = false;
        $this->startedOn = $this->lastUpdateOn = time();
    }
    
    /**
     *
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function getTransactionDto()
    {
        return $this->transactionDto;
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OneGoAPI $api 
     */
    public function setApi(OneGoSDK_Interface_OneGoAPI $api)
    {
        $this->api = $api;
    }
    
    /**
     *
     * @return boolean 
     */
    public function isStarted()
    {
        return (bool) $this->isStarted;
    }
    
    /**
     *
     * @return boolean 
     */
    public function isDelayed()
    {
        return (bool) $this->isDelayed;
    }
    
    /**
     *
     * @return boolean 
     */
    public function isExpired()
    {
        return $this->getExpiresIn() < 0;
    }
    
    /**
     *
     * @return integer Seconds till transaction expires 
     */
    public function getExpiresIn()
    {
        if ($this->ttlAutoRenew) {
            return $this->lastUpdateOn + $this->ttl - time();
        } else {
            return $this->startedOn + $this->ttl - time();
        }
    }

    public function getTtl()
    {
        return $this->ttl;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_TransactionIdDto Transaction ID DTO
     */
    public function getId()
    {
        return $this->getTransactionDto() ? 
            OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_TransactionIdDto', $this->getTransactionDto()->id) : 
            null;
    }
    
    /**
     *
     * @return OneGoSDK_Impl_Transaction
     */
    public function cancel()
    {
        return $this->end(OneGoSDK_DTO_TransactionEndDto::STATUS_CANCEL);
    }
    
    /**
     *
     * @return OneGoSDK_Impl_Transaction 
     */
    public function confirm()
    {
        return $this->end(OneGoSDK_DTO_TransactionEndDto::STATUS_CONFIRM);
    }
    
    /**
     *
     * @param integer $ttl Time (in seconds) to delay transaction for
     * @return OneGoSDK_Impl_Transaction 
     */
    public function delay($ttl)
    {
        $ttl = (int) $ttl;
        $res = $this->end(OneGoSDK_DTO_TransactionEndDto::STATUS_DELAY, $ttl);
        $this->touchTransaction($ttl);
        return $res;
    }
    
    /**
     *
     * @param OneGoSDK_Impl_Cart $cart
     * @return OneGoSDK_Impl_Transaction 
     */
    public function updateCart(OneGoSDK_Impl_Cart $cart)
    {
        $this->requireTransactionStarted();
        
        $req = new OneGoSDK_DTO_TransactionCartDto();
        $req->transactionId = $this->getId();
        $req->cartEntries = array_values($cart->getEntries());
        try {
            $dto = $this->api->updateCart($req);
            $this->setTransactionDto($dto);
            $this->touchTransaction();
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        return $this;
    }
    
    /**
     *
     * @param string $status OneGoSDK_DTO_TransactionEndDto status
     * @param mixed $ttl DelayTTL to delay transaction, else null 
     * @return OneGoSDK_Impl_Transaction 
     */
    protected function end($status, $ttl = null)
    {
        $this->requireTransactionStartedOrDelayed();
        
        if (($status == OneGoSDK_DTO_TransactionEndDto::STATUS_DELAY) && !(int) $ttl) {
            throw new OneGoSDK_Exception('TTL is required for transaction/end with status DELAY');
        }
        
        try {
            $req = new OneGoSDK_DTO_TransactionEndDto();
            $req->transactionId = $this->getId();
            $req->status = $status;
            $req->ttl = $ttl;
            $dto = $this->api->endTransaction($req);
            $this->setTransactionDto($dto);
            $this->touchTransaction();
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        if ($status == OneGoSDK_DTO_TransactionEndDto::STATUS_DELAY) {
            $this->setDelayedState($ttl);
        } else {
            $this->unsetDelayedState();
        }
        $this->setEndedState();
        return $this;
    }
    
    /**
     * Set transaction as delayed
     *
     * @param integer $delayTtl 
     */
    public function setDelayedState($delayTtl)
    {
        $this->isDelayed = true;
        $this->ttl = $delayTtl;
        $this->touchTransaction();
    }
    
    /**
     * Unset transaction as delayed
     */
    public function unsetDelayedState()
    {
        $this->isDelayed = false;
        $this->touchTransaction();
    }
    
    /**
     * Set transaction as ended
     */
    public function setEndedState()
    {
        $this->isStarted = false;
    }
    
    /**
     *
     * @param float $amount
     * @return OneGoSDK_Impl_Transaction 
     */
    public function spendPrepaid($amount)
    {
        $this->requireTransactionStarted();
        
        $req = new OneGoSDK_DTO_FundsOperationDto();
        $req->transactionId = $this->getId();
        $req->amount = $amount;
        try {
            $dto = $this->api->spendPrepaid($req);
            $this->setTransactionDto($dto);
            $this->touchTransaction();
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        return $this;
    }
    
    /**
     *
     * @return OneGoSDK_Impl_Transaction 
     */
    public function cancelSpendingPrepaid()
    {
        $this->requireTransactionStarted();
        
        $req = new OneGoSDK_DTO_FundsOperationCancelDto();
        $req->transactionId = $this->getId();
        try {
            $dto = $this->api->cancelSpendingPrepaid($req);
            $this->setTransactionDto($dto);
            $this->touchTransaction();
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        return $this;
    }
    
    /**
     * @param boolean $useHTTPBasicAuth Use HTTP Basic authentication method
     * @return OneGoSDK_Impl_Transaction 
     */
    public function get($useHTTPBasicAuth = false)
    {
        $req = $this->getId();
        try {
            if ($useHTTPBasicAuth) {
                $dto = $this->api->getTransactionBasic($req);
            } else {
                $dto = $this->api->getTransaction($req);
            }
            $this->setTransactionDto($dto);
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        return $this;
    }
    
    /**
     *
     * @param string $redemptionCode
     * @return OneGoSDK_Impl_Transaction 
     */
    public function useRedemptionCode($redemptionCode)
    {
        $this->requireTransactionStarted();
        
        $req = new OneGoSDK_DTO_UseRedemptionCodeRequestDto();
        $req->transactionId = $this->getId();
        $req->number = $redemptionCode;
        
        try {
            $dto = $this->api->useRedemptionCode($req);
            $this->setTransactionDto($dto);
            $this->touchTransaction();
        } catch (OneGoSDK_Exception $e) {
            throw $this->handleException($e);
        }
        
        return $this;
    }
    
    /**
     *
     * @param string $email
     * @return OneGoSDK_DTO_ModifiedCartDTO 
     */
    public function bindEmail($email)
    {
        $req = new OneGoSDK_DTO_TransactionBindRequestDto();
        $req->transactionId = $this->getId();
        $req->email = trim($email);
        $dto = $this->api->bind($req);
        $this->setTransactionDto($dto);
        $this->touchTransaction();
        return $this;
    }

    /**
     *
     * @param string $sessionToken
     * @return OneGoSDK_DTO_ModifiedCartDTO
     */
    public function bindSessionToken($sessionToken)
    {
        $req = new OneGoSDK_DTO_TransactionBindRequestDto();
        $req->transactionId = $this->getId();
        $req->sessionToken = $sessionToken;
        $dto = $this->api->bind($req);
        $this->setTransactionDto($dto);
        $this->touchTransaction();
        return $this;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_ModifiedCartDTO 
     */
    public function getModifiedCart()
    {
        return $this->getTransactionDto()->getModifiedCart();
    }
    
    public function getPrepaidAvailable()
    {
        $val = $this->getValue('buyerInfo', 'prepaidAvailable');
        return is_null($val) ? $val : (float) $val;
    }
    
    public function getReceiptNumber()
    {
        return $this->getValue('receiptNumber');
    }
    
    public function getExternalId()
    {
        return $this->getValue('externalId');
    }
    
    public function getOriginalAmount()
    {
        return $this->getAmountValue('originalAmount');
    }
    
    public function getCashAmount()
    {
        return $this->getAmountValue('cashAmount');
    }
    
    public function getPayableAmount()
    {
        return $this->getAmountValue('payableAmount');
    }
    
    public function getEntryDiscount()
    {
        return $this->getDiscountValue('entryDiscount');
    }
    
    public function getCartDiscount()
    {
        return $this->getDiscountValue('cartDiscount');
    }
    
    public function getTotalDiscount()
    {
        $modifiedCart = $this->getModifiedCart();
        return $modifiedCart ? $modifiedCart->getTotalDiscount() : null;
    }
    
    public function getPrepaidSpent()
    {
        $val = $this->getValue('modifiedCart', 'prepaidSpent');
        return !empty($val) ? (float) $val : null;
    }
    
    public function getPrepaidTopup()
    {
        $val = $this->getValue('modifiedCart', 'prepaidTopup');
        return !empty($val) ? (float) $val : null;
    }
    
    public function getPrepaidReceived()
    {
        return $this->getModifiedCart() ? $this->getModifiedCart()->getPrepaidReceived() : null;
    }
    
    public function getPrepaidAmountReceived()
    {
        $val = $this->getPrepaidReceived();
        if ($val) {
            return $val->getAmount()->getVisible();
        }
        return 0;
    }
    
    /**
     *
     * @return OneGoSDK_DTO_RedemptionCodeDto
     */
    public function getRedemptionCode()
    {
        return $this->getTransactionDto()->getRedemptionCode();
    }
    
    protected function requireTransactionStarted()
    {
        if (!$this->isStarted()) {
            throw new OneGoSDK_Exception('Transaction not started');
        }
    }
    
    protected function requireTransactionStartedOrDelayed()
    {
        if (!$this->isStarted() && !$this->isDelayed()) {
            throw new OneGoSDK_Exception('Transaction not started or delayed');
        }
    }
    
    protected function setTransactionDto(OneGoSDK_DTO_TransactionDto $dto)
    {
        $this->transactionDto = $dto;
    }
    
    protected function getValue($key, $key2 = null)
    {
        $dto = $this->getTransactionDto();
        if (isset($dto->$key)) {
            if ($key2) {
                if (isset($dto->$key->$key2)) {
                    return $dto->$key->$key2;
                }
            } else {
                return $dto->$key;
            }
        }
        return null;
    }
    
    protected function getDiscountValue($key)
    {
        $discount = $this->getValue('modifiedCart', $key);
        if (!empty($discount)) {
            $discount = OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_DiscountDto', $discount);
        }
        return $discount;
    }
    
    protected function getAmountValue($key)
    {
        $amount = $this->getValue('modifiedCart', $key);
        return isset($amount->visible) ? (float) $amount->visible : null;
    }
    
    protected function touchTransaction($newTtl = false)
    {
        $this->lastUpdateOn = time();
        $newTtl = (int) $newTtl;
        if ($newTtl) {
            $this->ttl = $newTtl;
        }
    }
    
    protected function handleException(Exception $e)
    {
        switch (get_class($e)) {
            case 'OneGoSDK_TransactionExpiredException':
                $this->isStarted = false;
                break;
        }
        return $e;
    }
}