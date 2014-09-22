<?php
class SpendPrepaidTest extends BaseOneGoAPITest
{
    public function testSpendPrepaid()
    {
        $request = $this->newFundsSpendingRequest();
        
        $this->gateway->expect('spendPrepaid', array($this->token, $request));
        $this->gateway->setReturnValue(
            'spendPrepaid',
            $this->newMinTransactionResponse(),
            array($this->token, $request)
        );

        $response = $this->unit->spendPrepaid($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
    
    public function testCancelSpendPrepaid()
    {
        $request = new OneGoSDK_DTO_FundsOperationCancelDto();
        $request->transactionId = $this->newTransactionId();
        
        $this->gateway->expect('cancelSpendingPrepaid', array($this->token, $request));
        $this->gateway->setReturnValue(
            'cancelSpendingPrepaid',
            $this->newMinTransactionResponse(),
            array($this->token, $request)
        );

        $response = $this->unit->cancelSpendingPrepaid($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}