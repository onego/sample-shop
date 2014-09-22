<?php
class UpdateCartTest extends BaseOneGoAPITest
{
    public function testUpdateCart()
    {
        $request = $this->newUpdateCartRequest();
        
        $this->gateway->expect('updateCart', array($this->token, $request));
        $this->gateway->setReturnValue(
            'updateCart',
            $this->newMinTransactionResponse(),
            array($this->token, $request)
        );

        $response = $this->unit->updateCart($request);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}