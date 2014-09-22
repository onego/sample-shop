<?php
class SimpleOneGoAPIUpdateCartTest extends BaseSimpleAPITest
{
    public function testUpdateCart()
    {   
        $req = $this->newTransactionBeginRequest();
        $transaction = $this->beginTransaction($req);
        $this->assertTrue($transaction->isStarted());
        
        $cart = $this->unit->newCart();
        $cart->addEntry('123', 5.50, 2, 11);
        $cart->addEntry('124', 3.20);
        
        $this->api->expectOnce('updateCart', array(
            new FieldEqualsExpectation('cartEntries', $cart->getEntries())
        ));
        $this->api->setReturnValue(
            'updateCart',
            $this->newTransactionMin($req)
        );
        
        $transaction->updateCart($cart);
    }
}