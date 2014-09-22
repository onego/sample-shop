<?php
class OneGoSDK_Exception extends Exception
{
    /**
     * Transform error DTO to matching exception
     */
    public static function fromError(OneGoSDK_Interface_ErrorDto $error)
    {
        if (get_class($error) == 'OneGoSDK_DTO_ErrorDto') {
            switch ($error->getCode())
            {
                case OneGoSDK_DTO_ErrorDto::API_INVALID_INPUT:
                    return new OneGoSDK_InvalidInputException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_UNAUTHORIZED:
                    return new OneGoSDK_UnauthorizedException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_UNAUTHORIZED_TERMINAL:
                    return new OneGoSDK_UnauthorizedTerminalException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_BLOCKED_MERCHANT:
                    return new OneGoSDK_BlockedMerchantException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_BLOCKED_BUYER:
                    return new OneGoSDK_BlockedBuyerException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_BLOCKED_BIM:
                    return new OneGoSDK_BlockedBimException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_BIM_NOT_FOUND:
                    return new OneGoSDK_BIMNotFoundException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_OPERATION_NOT_ALLOWED:
                    return new OneGoSDK_OperationNotAllowedException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_TRANSACTION_NOT_FOUND:
                    return new OneGoSDK_TransactionNotFoundException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_INVALID_OAUTH_TOKEN:
                    return new OneGoSDK_InvalidOAuthTokenException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_EXPIRED_OAUTH_TOKEN:
                    return new OneGoSDK_ExpiredOAuthTokenException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_INSUFFICIENT_OAUTH_TOKEN_SCOPE:
                    return new OneGoSDK_InsufficientOAuthTokenScopeException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_TRANSACTION_EXPIRED:
                    return new OneGoSDK_TransactionExpiredException($error->getMessage());
                
                case OneGoSDK_DTO_ErrorDto::API_REDEEM_CODE_NOT_FOUND:
                    return new OneGoSDK_RedemptionCodeNotFoundException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_NO_SERVICE_SUBSCRIPTION:
                    return new OneGoSDK_NoServiceSubscriptionException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_STALE_STATE:
                    return new OneGoSDK_StaleStateException($error->getMessage());

                case OneGoSDK_DTO_ErrorDto::API_ERROR:
                default:
                    return new OneGoSDK_Exception($error->getMessage());
            }
        } else if (get_class($error) == 'OneGoSDK_DTO_OAuthErrorDto') {
            switch ($error->getCode())
            {
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_INVALID_CLIENT:
                    return new OneGoSDK_OAuthInvalidClientException($error->getMessage());
                    
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_INVALID_REQUEST:
                    return new OneGoSDK_OAuthInvalidRequestException($error->getMessage());
                
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_INVALID_GRANT:
                    return new OneGoSDK_OAuthInvalidGrantException($error->getMessage());
                
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_UNAUTHORIZED_CLIENT:
                    return new OneGoSDK_OAuthUnauthorizedClientException($error->getMessage());
                
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_UNSUPPORTED_GRANT_TYPE:
                    return new OneGoSDK_OAuthUnsupportedGrantTypeException($error->getMessage());
                
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_INVALID_SCOPE:
                    return new OneGoSDK_OAuthInvalidScopeException($error->getMessage());
                    
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_ACCESS_DENIED:
                    return new OneGoSDK_OAuthAccessDeniedException($error->getMessage());
                    
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_UNSUPPORTED_RESPONSE_TYPE:
                    return new OneGoSDK_OAuthUnsupportedResponseTypeException($error->getMessage());
                    
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_SERVER_ERROR:
                    return new OneGoSDK_OAuthServerErrorException($error->getMessage());
                
                case OneGoSDK_DTO_OAuthErrorDto::OAUTH_TEMPORARILY_UNAVAILABLE:
                    return new OneGoSDK_OAuthTemporarilyUnavailableException($error->getMessage());
                
                default:
                    return new OneGoSDK_OAuthException($error->getMessage());
            }
        } else {
            return new OneGoSDK_Exception($error->getMessage());
        }
    }
}

// API exceptions
class OneGoSDK_HTTPConnectionTimeoutException extends OneGoSDK_Exception {}

class OneGoSDK_NoContentException extends OneGoSDK_Exception {}

class OneGoSDK_InvalidInputException extends OneGoSDK_Exception {}

class OneGoSDK_UnauthorizedException extends OneGoSDK_Exception {}

class OneGoSDK_UnauthorizedTerminalException extends OneGoSDK_UnauthorizedException {}

class OneGoSDK_InvalidOAuthTokenException extends OneGoSDK_UnauthorizedException {}

class OneGoSDK_ExpiredOAuthTokenException extends OneGoSDK_UnauthorizedException {}

class OneGoSDK_ForbiddenException extends OneGoSDK_Exception {}

class OneGoSDK_BlockedMerchantException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_BlockedBuyerException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_BlockedBimException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_BIMNotFoundException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_OperationNotAllowedException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_RedemptionCodeNotFoundException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_InsufficientOAuthTokenScopeException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_TransactionExpiredException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_TransactionNotFoundException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_NoServiceSubscriptionException extends OneGoSDK_ForbiddenException {}

class OneGoSDK_StaleStateException extends OneGoSDK_Exception {}

// OAuth exceptions

class OneGoSDK_OAuthException extends OneGoSDK_Exception {}

class OneGoSDK_OAuthInvalidRequestException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthInvalidClientException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthInvalidGrantException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthUnauthorizedClientException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthUnsupportedGrantTypeException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthInvalidScopeException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthAccessDeniedException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthUnsupportedResponseTypeException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthServerErrorException extends OneGoSDK_OAuthException {}

class OneGoSDK_OAuthTemporarilyUnavailableException extends OneGoSDK_OAuthException {}