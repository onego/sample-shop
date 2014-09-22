<?php
class SimpleOneGoAPITransactionGettersTest extends BaseSimpleAPITest
{
    public function testGettersSuccess()
    {   
        $req = $this->newTransactionBeginRequest();
        $req->currencyCode = 'BYR';
        $this->api->setReturnValue('beginTransaction', $this->newTransactionFull($req));
        $t = $this->unit->beginTransaction($req->receiptNumber);
        
        $this->assertEqual($t->getPrepaidAvailable(), 123.45);
        $this->assertEqual($t->getReceiptNumber(), $req->receiptNumber);
        $this->assertIsA($t->getExternalId(), 'OneGoSDK_DTO_TransactionIdDto');
        $this->assertEqual($t->getOriginalAmount(), 345.67);
        $this->assertEqual($t->getCashAmount(), 234.56);
        $this->assertEqual($t->getPayableAmount(), 222.22);
        $this->assertIsA($t->getEntryDiscount(), 'OneGoSDK_DTO_DiscountDto');
        $this->assertEqual($t->getEntryDiscount()->getAmount()->getVisible(), 11.11);
        $this->assertEqual($t->getEntryDiscount()->getPercents(), 5.55);
        $this->assertIsA($t->getCartDiscount(), 'OneGoSDK_DTO_DiscountDto');
        $this->assertIsA($t->getTotalDiscount(), 'OneGoSDK_DTO_DiscountDto');
        $this->assertEqual($t->getPrepaidSpent(), 23.45);
        $this->assertEqual($t->getPrepaidTopup(), 45.67);
        $this->assertEqual($t->getPrepaidAmountReceived(), 66.66);        
    }
    
    public function testGettersFail()
    {
        $req = $this->newTransactionBeginRequest();
        $this->api->setReturnValue('beginTransaction', $this->newTransactionMin($req));
        $t = $this->unit->beginTransaction($req->receiptNumber);
        
        $this->assertNull($t->getPrepaidAvailable());
        $this->assertNull($t->getExternalId());
        $this->assertNull($t->getOriginalAmount());
        $this->assertNull($t->getCashAmount());
        $this->assertNull($t->getPayableAmount());
        $this->assertNull($t->getEntryDiscount());
        $this->assertNull($t->getCartDiscount());
        $this->assertNull($t->getTotalDiscount());
        $this->assertNull($t->getPrepaidSpent());
        $this->assertNull($t->getPrepaidTopup());
        $this->assertEqual($t->getPrepaidAmountReceived(), 0);
    }
    
}