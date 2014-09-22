<?php
class SimpleOneGoAPIGetTransactionTest extends BaseSimpleAPITest
{
    public function testGetTransaction()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        
        $this->api->expectOnce('getTransaction', array(
            new IsAExpectation('OneGoSDK_DTO_TransactionIdDto')
        ));
        $this->api->expectOnce('getTransaction', array(
            new FieldEqualsExpectation('id', $transaction->getId()->id)
        ));
        $this->api->setReturnValue(
            'getTransaction',
            $this->newTransactionMin($req)
        );
        
        $transaction->get();
    }

    public function testGetTransactionBasicAuth()
    {
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);

        $this->api->expectOnce('getTransactionBasic', array(
            new IsAExpectation('OneGoSDK_DTO_TransactionIdDto')
        ));
        $this->api->expectOnce('getTransactionBasic', array(
            new FieldEqualsExpectation('id', $transaction->getId()->id)
        ));
        $this->api->setReturnValue(
            'getTransactionBasic',
            $this->newTransactionMin($req)
        );

        $transaction->get(true);
    }
    
}