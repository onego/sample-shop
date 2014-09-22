<?php
class CancelTransactionTest extends BaseOneGoAPITest
{
    public function testCancelTransaction()
    {
        $request = $this->newTransactionEndRequest('CANCEL');
        
        $this->gateway->expect('endTransactionBasic', array($request));
        $this->gateway->setReturnValue(
            'endTransactionBasic',
            $this->newMinTransactionResponse(),
            array($request)
        );

        $response = $this->unit->endTransaction($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}