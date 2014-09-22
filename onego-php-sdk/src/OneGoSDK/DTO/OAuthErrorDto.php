<?php
final class OneGoSDK_DTO_OAuthErrorDto implements OneGoSDK_Interface_ErrorDto
{
    const OAUTH_INVALID_REQUEST     = 'invalid_request';
    const OAUTH_INVALID_CLIENT      = 'invalid_client';
    const OAUTH_INVALID_GRANT       = 'invalid_grant';
    const OAUTH_UNAUTHORIZED_CLIENT = 'unauthorized_client';
    const OAUTH_UNSUPPORTED_GRANT_TYPE = 'unsupported_grant_type';
    const OAUTH_INVALID_SCOPE       = 'invalid_scope';
    const OAUTH_ACCESS_DENIED       = 'access_denied';
    const OAUTH_UNSUPPORTED_RESPONSE_TYPE = 'unsupported_response_type';
    const OAUTH_SERVER_ERROR        = 'server_error';
    const OAUTH_TEMPORARILY_UNAVAILABLE = 'temporarily_unavailable';
    
    public $error_description;

    public $error_uri;

    public $error;
    
    public function getCode() {
        return $this->error;
    }
    
    public function getMessage() {
        return $this->error_description;
    }
}
