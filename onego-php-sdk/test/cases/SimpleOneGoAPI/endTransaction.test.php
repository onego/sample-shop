<?php
class SimpleOneGoAPIEndTransactionTest extends BaseSimpleAPITest
{
    public function testCancelTransaction()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        $this->assertTrue($transaction->isStarted());
        
        $this->api->expectOnce('endTransaction', array(
            new IsAExpectation('OneGoSDK_DTO_TransactionEndDto')
        ));
        $this->api->setReturnValue(
            'endTransaction',
            $this->newTransactionMin($req)
        );
        $transaction->cancel();
        
        $this->assertFalse($transaction->isStarted());
        
        // may not cancel nonstarted transaction
        $this->expectException('OneGoSDK_Exception');
        $transaction->cancel();
    }
    
    public function testConfirmTransaction()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        $this->assertTrue($transaction->isStarted());
        
        $this->api->expectOnce('endTransaction', array(
            new IsAExpectation('OneGoSDK_DTO_TransactionEndDto')
        ));
        $this->api->setReturnValue(
            'endTransaction',
            $this->newTransactionMin($req)
        );
        $transaction->confirm();
        
        
        $this->assertFalse($transaction->isStarted());
        
        $this->expectException('OneGoSDK_Exception');
        $transaction->confirm();
        
        
    }
}