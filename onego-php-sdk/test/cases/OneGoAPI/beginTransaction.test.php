<?php
class BeginTransactionTest extends BaseOneGoAPITest
{
    public function testRequestResponse()
    {
        $request = $this->newMinTransactionBeginRequest();
        
        $this->gateway->expect('beginTransaction', array($this->token, $request));
        $this->gateway->setReturnValue(
            'beginTransaction',
            $this->newMinTransactionResponse(),
            array($this->token, $request)
        );

        $response = $this->unit->beginTransaction($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}