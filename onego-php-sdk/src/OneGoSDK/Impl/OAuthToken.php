<?php
abstract class OneGoSDK_Impl_OAuthToken 
    implements OneGoSDK_Interface_OAuthToken, OneGoSDK_Interface_HttpAuthorization
        
{
    public $accessToken;
    public $refreshToken;
    public $type;
    public $expiresIn = 3600;
    public $issuedOn;
    public $scopes = array();
 
    /**
     *
     * @param OneGoSDK_DTO_OAuthTokenDto $token
     */
    public function __construct(OneGoSDK_DTO_OAuthTokenDto $token)
    {
        $this->importDto($token);
        $this->issuedOn = time();
    }
    
    /**
     *
     * @return boolean 
     */
    public function isExpired()
    {
        return $this->issuedOn + $this->expiresIn < time();
    }
    
    /**
     *
     * @param string $scope
     * @return boolean 
     */
    public function hasScope($scope)
    {
        return in_array($scope, $this->scopes);
    }
    
    /**
     *
     * @param array $scopes 
     */
    public function setScopes($scopes)
    {
        $this->scopes = is_array($scopes) ? $scopes : array($scopes);
    }
    
    /**
     *
     * @return array Token scopes 
     */
    public function getScopes()
    {
        return $this->scopes;
    }
    
    /**
     * Load from DTO
     *
     * @param OneGoSDK_DTO_OAuthTokenDto $token 
     */
    protected function importDto(OneGoSDK_DTO_OAuthTokenDto $token)
    {
        // copy fields
        $properties_map = array(
            'accessToken'   => 'access_token',
            'refreshToken'  => 'refresh_token',
            'type'          => 'token_type',
            'expiresIn'     => 'expires_in',
        );
        foreach ($properties_map as $key => $val) {
            if (isset($token->$val)) {
                $this->$key = $token->$val;
            }
        }
        
        // explode scopes
        if (isset($token->scope) && strlen(trim($token->scope))) {
            $this->scopes = explode(' ', $token->scope);
        }
    }
    
    /**
     * Factory for OneGoSDK_DTO_OAuthTokenDto objects
     *
     * @param OneGoSDK_DTO_OAuthTokenDto $token
     * @return OneGoSDK_DTO_OAuthTokenDto Specific child class, detected by token type 
     */
    public static function createByDto(OneGoSDK_DTO_OAuthTokenDto $token)
    {
        $type = strtolower($token->token_type);
        if ($type == 'bearer') {
            return new OneGoSDK_Impl_OAuthTokenBearer($token);
        } else {
            throw new OneGoSDK_Exception('Unknown OAuth token type');
        }

    }
}