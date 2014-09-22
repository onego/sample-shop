<?php
class TransformationTest extends BaseUnitTestCase
{
    public function testRealTransformation()
    {
        $source = new stdClass();
        $source->visible    = '123.45';
        $source->precise    = '123.4567';

        $transformed = OneGoSDK_Impl_Transform::transform(
            'OneGoSDK_DTO_AmountDto',
            $source
        );

        $this->assertIsA($transformed, 'OneGoSDK_DTO_AmountDto');

        $expect = new OneGoSDK_DTO_AmountDto();
        foreach ($source as $field => $value) {
            $expect->$field  = $value;
        }

        $this->assertEqual($expect, $transformed);
    }
    
    public function testErrorTransformations()
    {
        $errors = array(
            'API_INVALID_INPUT' => 'OneGoSDK_InvalidInputException',
            'API_UNAUTHORIZED' => 'OneGoSDK_UnauthorizedException',
            'API_UNAUTHORIZED_TERMINAL' => 'OneGoSDK_UnauthorizedTerminalException',
            'API_INVALID_OAUTH_TOKEN' => 'OneGoSDK_InvalidOAuthTokenException',
            'API_EXPIRED_OAUTH_TOKEN' => 'OneGoSDK_ExpiredOAuthTokenException',
            'API_BLOCKED_MERCHANT' => 'OneGoSDK_BlockedMerchantException',
            'API_BLOCKED_BUYER' => 'OneGoSDK_BlockedBuyerException',
            'API_BLOCKED_BIM' => 'OneGoSDK_BlockedBimException',
            'API_BIM_NOT_FOUND' => 'OneGoSDK_BIMNotFoundException',
            'API_OPERATION_NOT_ALLOWED' => 'OneGoSDK_OperationNotAllowedException',
            'API_TRANSACTION_NOT_FOUND' => 'OneGoSDK_TransactionNotFoundException',
            'API_INSUFFICIENT_OAUTH_TOKEN_SCOPE' => 'OneGoSDK_InsufficientOAuthTokenScopeException',
            'API_TRANSACTION_EXPIRED' => 'OneGoSDK_TransactionExpiredException',
        );
        
        $exceptions_count = 0;
        foreach ($errors as $errorCode => $expectedException) {
            $errorDto               = new stdClass();
            $errorDto->cause        = 'trololo';
            $errorDto->message      = 'error - '.$errorCode;
            $errorDto->errorCode    = $errorCode;

            try {
                $res = OneGoSDK_Impl_Transform::transform(
                    'OneGoSDK_DTO_TransactionDto',
                    $errorDto
                );
            } catch (OneGoSDK_Exception $e) {
                $this->assertIsA($e, $expectedException);
                $exceptions_count++;
            }
        }
        
        $this->assertEqual(count($errors), $exceptions_count);
    }
}