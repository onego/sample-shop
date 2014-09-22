<?php
class GetTransactionTest extends BaseOneGoAPITest
{
    public function testGetTransaction()
    {
        $request = $this->newTransactionId();
        
        $this->gateway->expect('getTransaction', array($this->token, $request));
        $this->gateway->setReturnValue(
            'getTransaction',
            $this->newMinTransactionResponse(),
            array($this->token, $request)
        );

        $response = $this->unit->getTransaction($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }

    public function testGetTransactionBasic()
    {
        $request = $this->newTransactionId();

        $this->gateway->expect('getTransactionBasic', array($request));
        $this->gateway->setReturnValue(
            'getTransactionBasic',
            $this->newMinTransactionResponse(),
            array($request)
        );

        $response = $this->unit->getTransactionBasic($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}