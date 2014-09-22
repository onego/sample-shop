<?php
class DiscountDtoTest extends UnitTestCase
{
    public function testGetAmount()
    {
        $unit = new OneGoSDK_DTO_DiscountDto();
        $unit->amount = OneGoSDK_DTO_AmountDto::asCash(123.45);
        $unit->percent = 12.5;
        
        $this->assertEqual($unit->getAmount()->getVisible(), 123.45);
        $this->assertEqual($unit->getPercents(), 12.5);
    }
    
    public function testGetAmountEmpty()
    {
        $unit = new OneGoSDK_DTO_DiscountDto();
        
        $this->assertNull($unit->getAmount());
        $this->assertNull($unit->getPercents());
    }
}