<?php
class OneGoSDK_Impl_OAuthTokenBearer extends OneGoSDK_Impl_OAuthToken
{
    /**
     *
     * @return string HTTP authorization header string
     */
    public function getAuthorizationHeader()
    {
        return 'Authorization: Bearer '.$this->accessToken;
    }
}