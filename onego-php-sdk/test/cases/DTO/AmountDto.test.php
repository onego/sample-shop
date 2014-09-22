<?php
class AmountDtoTest extends UnitTestCase
{
    public function testAutoTrim()
    {
        $amount = OneGoSDK_DTO_AmountDto::asCash('123.456789');
        $this->assertEqual('123.45', $amount->visible);
        $this->assertEqual('123.4567', $amount->precise);
    }

    public function testAutoTrimWithShorterInput()
    {
        $amount = OneGoSDK_DTO_AmountDto::asCash('123');
        $this->assertEqual('123', $amount->visible);
        $this->assertEqual('123', $amount->precise);
    }

    public function testInvalidInput()
    {
        $this->expectException(new IsAExpectation('OneGoSDK_Exception'));
        $amount = OneGoSDK_DTO_AmountDto::asCash('some crap');
    }
}