<?php
class SimpleOneGoAPISpendPrepaidTest extends BaseSimpleAPITest
{
    public function testSpendPrepaid()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        
        $amount = 123.45;
        
        $this->api->expectOnce('spendPrepaid', array(
            new IsAExpectation('OneGoSDK_DTO_FundsOperationDto')
        ));
        $this->api->expectOnce('spendPrepaid', array(
            new FieldEqualsExpectation('amount', $amount)
        ));
        $this->api->setReturnValue(
            'spendPrepaid',
            $this->newTransactionMin($req)
        );
        
        $transaction->spendPrepaid($amount);
    }
    
    public function testCancelSpendPrepaid()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        
        $this->api->expectOnce('cancelSpendingPrepaid', array(
            new IsAExpectation('OneGoSDK_DTO_FundsOperationCancelDto')
        ));
        $this->api->setReturnValue(
            'cancelSpendingPrepaid',
            $this->newTransactionMin($req)
        );
        
        $transaction->cancelSpendingPrepaid();
    }
}