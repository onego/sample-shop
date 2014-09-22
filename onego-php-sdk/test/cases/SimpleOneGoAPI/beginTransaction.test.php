<?php
class SimpleOneGoAPIBeginTransactionTest extends BaseSimpleAPITest
{
    public function testBeginTransaction()
    {        
        $request = $this->newTransactionBeginRequest();
        
        $this->api->expectOnce('beginTransaction', array($request));
        $transaction = $this->beginTransaction($request);
        
        $this->assertIsA($transaction, 'OneGoSDK_Impl_Transaction');
        $this->assertIsA($this->unit->getTransaction(), 'OneGoSDK_Impl_Transaction');
        
        $this->assertEqual($transaction->getTransactionDto()->terminalId, $request->terminalId);
        
        // disallow starting transaction when already started
        $this->expectException('OneGoSDK_Exception');
        $transaction = $this->beginTransaction($request);
    }
    
    public function testTransactionTtl()
    {
        $request = $this->newTransactionBeginRequest();
        $this->api->expectOnce(
                'beginTransaction',
                array(new FieldEqualsExpectation('ttl', $this->getApiConfig()->transactionTtl)));
        
        $this->api->setReturnValue('beginTransaction', $this->newTransactionFull($request));
        $transaction = $this->unit->beginTransaction('rcpt');
    }
    
}