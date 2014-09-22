<?php
class SimpleOneGoAPICommonTest extends BaseSimpleAPITest
{
    public function testSetTransaction()
    {
        $this->assertNull($this->unit->getTransaction());
        
        $transaction = new OneGoSDK_Impl_Transaction(new OneGoSDK_DTO_TransactionDto(), 
                $this->getApiConfig()->transactionTtl, $this->getApiConfig()->transactionTtlAutoRenew);
        $this->unit->setTransaction($transaction);
        
        $tx = $this->unit->getTransaction();
        
        $this->assertIsA($tx, 'OneGoSDK_Impl_Transaction');
    }
    
    public function testTransactionStarted()
    {
        $this->assertFalse($this->unit->isTransactionStarted());
        
        $transaction = new OneGoSDK_Impl_Transaction(new OneGoSDK_DTO_TransactionDto(), 
                $this->getApiConfig()->transactionTtl, $this->getApiConfig()->transactionTtlAutoRenew);
        $this->unit->setTransaction($transaction);
                
        $this->assertTrue($this->unit->isTransactionStarted());
    }
    
    public function testTransactionTtl()
    {
        $transaction = new OneGoSDK_Impl_Transaction(new OneGoSDK_DTO_TransactionDto(), 
                $this->getApiConfig()->transactionTtl, $this->getApiConfig()->transactionTtlAutoRenew);
        
        $this->assertTrue($transaction->getExpiresIn() == $this->getApiConfig()->transactionTtl);
        
        sleep(1);
        
        $this->assertTrue($transaction->getExpiresIn() + 1 == $this->getApiConfig()->transactionTtl);
    }
    
    public function testNewCart()
    {
        $cart = $this->unit->newCart();
        $this->assertIsA($cart, 'OneGoSDK_Impl_Cart');
        $this->assertTrue(count($cart) == 0);
    }
}