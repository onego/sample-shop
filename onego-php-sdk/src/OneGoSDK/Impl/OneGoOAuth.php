<?php
class OneGoSDK_Impl_OneGoOAuth implements OneGoSDK_Interface_OneGoOAuth
{
    const SCOPE_RECEIVE_ONLY = 'pos.receive-only';
    const SCOPE_USE_BENEFITS = 'pos.use-benefits';

    const TOKEN_TYPE_ACCESS = 'access';
    const TOKEN_TYPE_REFRESH = 'refresh';
    
    private $gateway;

    /**
     *
     * @param OneGoSDK_Impl_OAuthGateway $gateway 
     */
    public function __construct(OneGoSDK_Impl_OAuthGateway $gateway)
    {
        $this->gateway = $gateway;
    }
    
    /**
     *
     * @return OneGoSDK_OAuthConfig
     */
    public function getConfig()
    {
        return $this->gateway->getConfig();
    }
    
    /**
     * Request for OAuth token
     *
     * @param OneGoSDK_DTO_OAuthTokenRequestDto $request
     * @return OneGoSDK_DTO_OAuthTokenDto 
     */
    public function requestAccessToken(OneGoSDK_DTO_OAuthTokenRequestDto $request)
    {
        $resp = $this->gateway->requestAccessToken($request);
        
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_OAuthTokenDto',
            $resp,
            'OneGoSDK_DTO_OAuthErrorDto'
        );
    }
    
    /**
     *
     * @param OneGoSDK_DTO_OAuthTokenrefreshRequestDto $request
     * @return OneGoSDK_DTO_OAuthTokenDto 
     */
    public function refreshAccessToken(OneGoSDK_DTO_OAuthTokenRefreshRequestDto $request)
    {
        $resp = $this->gateway->refreshAccessToken($request);
        
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_OAuthTokenDto',
            $resp,
            'OneGoSDK_DTO_OAuthErrorDto'
        );
    }
    
    /**
     *
     * @param string $token Token number
     * @return array 
     */
    public function checkAccessToken($token)
    {
        $resp = $this->gateway->checkAccessToken($token);

        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_OAuthTokenDto',
            $resp,
            'OneGoSDK_DTO_OAuthErrorDto'
        );
    }
    
    /**
     * Request OAuth token by Redemption Code
     *
     * @param OneGoSDK_DTO_OAuthTokenByRedemptionCodeRequestDto $request
     * @return OneGoSDK_DTO_OAuthTokenDto 
     */
    public function requestAccessTokenByRedemptionCode(OneGoSDK_DTO_OAuthTokenByRedemptionCodeRequestDto $request)
    {
        $resp = $this->gateway->requestAccessTokenByRedemptionCode($request);
        
        return OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_OAuthTokenDto',
            $resp,
            'OneGoSDK_DTO_OAuthErrorDto'
        );
    }

    /**
     *
     * @param string $token Token number
     * @param OneGoSDK_DTO_OAuthRevokeTokenRequestDto $request
     * @return stdClass
     */
    public function revokeToken($token, OneGoSDK_DTO_OAuthRevokeTokenRequestDto $request)
    {
        try {
            $resp = $this->gateway->revokeToken($token, $request);
        } catch (OneGoSDK_NoContentException $e) {
            return true;
        }

        // must be error if content not empty
        return OneGoSDK_Exception::fromError(
                OneGoSDK_Impl_Transform::transform(
                    'OneGoSDK_DTO_OAuthErrorDto',
                    $resp));
    }
}