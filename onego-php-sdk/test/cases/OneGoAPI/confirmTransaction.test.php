<?php
class ConfirmTransactionTest extends BaseOneGoAPITest
{
    public function testConfirmTransaction()
    {
        $request = $this->newTransactionEndRequest('CONFIRM');
        
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