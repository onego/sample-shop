<?php
class OtherTest extends BaseOneGoAPITest
{
    public function testBind()
    {
        $req = new OneGoSDK_DTO_TransactionBindRequestDto();
        $req->email = 'aaa@bbb.ccc';
        $req->transactionId = $this->newTransactionId();
        
        $this->gateway->expect('bind', array($req));
        $this->gateway->throwAt(0, 'bind', new OneGoSDK_NoContentException());

        $response = $this->unit->bind($req);

        $this->assertIsA($response, 'boolean');
        $this->assertEqual($response, true);
    }
    
    public function testBindNew()
    {
        $req = new OneGoSDK_DTO_TransactionBindNewRequestDto();
        $req->email = 'aaa@bbb.ccc';
        $req->cartEntries = $this->newCart(10);
        
        $this->gateway->expect('bindNew', array($req));
        $this->gateway->setReturnValue(
            'bindNew',
            $this->newMinTransactionResponse(),
            array($req)
        );

        $response = $this->unit->bindNew($req);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
    
    public function testUseRedemptionCode()
    {
        $req = new OneGoSDK_DTO_UseRedemptionCodeRequestDto();
        $req->number = '1234';
        $req->transactionId = $this->newTransactionId();
        
        $this->gateway->expect('useRedemptionCode', array($this->token, $req));
        $this->gateway->setReturnValue(
            'useRedemptionCode',
            $this->newMinTransactionResponse(),
            array($this->token, $req)
        );

        $response = $this->unit->useRedemptionCode($req);

        $this->assertIsA($response, 'OneGoSDK_DTO_TransactionDto');
        $this->assertEqual($response->terminalId, $this->getApiConfig()->terminalId);
    }
}