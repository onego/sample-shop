<?php
class OneGoSDK_Impl_OneGoAPI implements OneGoSDK_Interface_OneGoAPI
{
    protected $gateway;
    protected $oauthToken;

    /**
     *
     * @param OneGoSDK_Interface_Gateway $gateway 
     */
    public function __construct(OneGoSDK_Interface_Gateway $gateway)
    {
        $this->gateway = $gateway;
    }
    
    /**
     *
     * @param OneGoSDK_Interface_OAuthToken $token 
     */
    public function setOAuthToken(OneGoSDK_Interface_OAuthToken $token)
    {
        $this->oauthToken = $token;
    }

    /**
     *
     * @return OneGoSDK_APIConfig Gateway config 
     */
    public function getConfig()
    {
        return $this->gateway->getConfig();
    }
    
    /**
     *
     * @return OneGoSDK_Interface_OAuthToken 
     */
    public function getOAuthToken()
    {
        if (empty($this->oauthToken)) {
            throw new OneGoSDK_Exception('OAuthToken not set!');
        }
        return $this->oauthToken;
    }

    /**
     * API call: /transaction/begin
     *
     * @param OneGoSDK_DTO_TransactionBeginRequestDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function beginTransaction(OneGoSDK_DTO_TransactionBeginRequestDto $request)
    {
        if (!empty($request->cartEntries)) {
            $request->cartEntries = array_values($request->cartEntries);
        }
        
        OneGoSDK_Impl_Validator::validateRequest($request);

        $resp = $this->gateway->beginTransaction($this->getOAuthToken(), $request);
        
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $resp
        );
    }
    
    /**
     * API call: /transaction/end
     *
     * @param OneGoSDK_DTO_TransactionEndDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function endTransaction(OneGoSDK_DTO_TransactionEndDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->endTransactionBasic($request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /transaction (update cart)
     *
     * @param OneGoSDK_DTO_TransactionCartDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function updateCart(OneGoSDK_DTO_TransactionCartDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->updateCart($this->getOAuthToken(), $request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /transaction/prepaid/spend
     *
     * @param OneGoSDK_DTO_FundsOperationDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function spendPrepaid(OneGoSDK_DTO_FundsOperationDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->spendPrepaid($this->getOAuthToken(), $request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /transaction/prepaid/spending/cancel
     *
     * @param OneGoSDK_DTO_FundsOperationCancelDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function cancelSpendingPrepaid(OneGoSDK_DTO_FundsOperationCancelDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->cancelSpendingPrepaid($this->getOAuthToken(), $request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /transaction/get (using OAuth token authentication)
     *
     * @param OneGoSDK_DTO_TransactionIdDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function getTransaction(OneGoSDK_DTO_TransactionIdDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->getTransaction($this->getOAuthToken(), $request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }

    /**
     * API call: /transaction/get (using HTTP Basic authentication)
     *
     * @param OneGoSDK_DTO_TransactionIdDto $request
     * @return OneGoSDK_DTO_TransactionDto
     */
    public function getTransactionBasic(OneGoSDK_DTO_TransactionIdDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);

        $res = $this->gateway->getTransactionBasic($request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /awards
     *
     * @param OneGoSDK_DTO_CalculateAwardsDto $request
     * @return OneGoSDK_DTO_ModifiedCartDto 
     */
    public function awards(OneGoSDK_DTO_CalculateAwardsDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->awards($request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_ModifiedCartDto',
            $res
        );
    }
    
    /**
     * API call: /transaction/bind/new
     *
     * @param OneGoSDK_DTO_TransactionBindNewRequestDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function bindNew(OneGoSDK_DTO_TransactionBindNewRequestDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->bindNew($request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
    
    /**
     * API call: /transaction/bind
     *
     * @param OneGoSDK_DTO_TransactionBindRequestDto $request
     * @return boolean True on success 
     */
    public function bind(OneGoSDK_DTO_TransactionBindRequestDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);

        try {
            $res = $this->gateway->bind($request);
            return OneGoSDK_Impl_Transform::transform(
                'OneGoSDK_DTO_TransactionDto',
                $res
            );
        } catch (OneGoSDK_NoContentException $e) {
            return true;
        }
        return false;
    }
    
    /**
     * API call: /transaction/redemption-code/use
     *
     * @param OneGoSDK_DTO_UseRedemptionCodeRequestDto $request
     * @return OneGoSDK_DTO_TransactionDto 
     */
    public function useRedemptionCode(OneGoSDK_DTO_UseRedemptionCodeRequestDto $request)
    {
        OneGoSDK_Impl_Validator::validateRequest($request);
        
        $res = $this->gateway->useRedemptionCode($this->getOAuthToken(), $request);
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_TransactionDto',
            $res
        );
    }
}