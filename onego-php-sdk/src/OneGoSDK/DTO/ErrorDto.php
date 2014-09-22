<?php

final class OneGoSDK_DTO_ErrorDto implements OneGoSDK_Interface_ErrorDto
{
    // error 500
    const API_ERROR                 = 'API_ERROR';
    
    // error 400
    const API_INVALID_INPUT         = 'API_INVALID_INPUT';
    
    // error 401
    const API_UNAUTHORIZED          = 'API_UNAUTHORIZED';
    const API_UNAUTHORIZED_TERMINAL = 'API_UNAUTHORIZED_TERMINAL';
    const API_INVALID_OAUTH_TOKEN   = 'API_INVALID_OAUTH_TOKEN';
    const API_EXPIRED_OAUTH_TOKEN   = 'API_EXPIRED_OAUTH_TOKEN';
    const API_NO_SERVICE_SUBSCRIPTION = 'API_NO_SERVICE_SUBSCRIPTION';
    
    // error 403
    const API_BLOCKED_MERCHANT      = 'API_BLOCKED_MERCHANT';
    const API_BLOCKED_BUYER         = 'API_BLOCKED_BUYER';
    const API_BLOCKED_BIM           = 'API_BLOCKED_BIM';
    const API_BIM_NOT_FOUND         = 'API_BIM_NOT_FOUND';
    const API_OPERATION_NOT_ALLOWED = 'API_OPERATION_NOT_ALLOWED';
    const API_TRANSACTION_EXPIRED   = 'API_TRANSACTION_EXPIRED';
    const API_TRANSACTION_NOT_FOUND = 'API_TRANSACTION_NOT_FOUND';
    const API_INSUFFICIENT_OAUTH_TOKEN_SCOPE = 'API_INSUFFICIENT_OAUTH_TOKEN_SCOPE';
    const API_REDEEM_CODE_NOT_FOUND = 'API_REDEMPTION_CODE_NOT_FOUND';

    // error 409
    const API_STALE_STATE           = 'API_STALE_STATE';

    public $message;

    public $cause;

    public $errorCode;
    
    public function getCode() {
        return $this->errorCode;
    }
    
    public function getMessage() {
        return $this->message;
    }
}
