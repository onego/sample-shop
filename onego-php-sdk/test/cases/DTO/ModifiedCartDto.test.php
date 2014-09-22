<?php
class ModifiedCartDtoTest extends UnitTestCase
{
    public function testAddGet()
    {
        $cart = new OneGoSDK_DTO_ModifiedCartDto();
        $this->assertTrue(count($cart->getEntries()) == 0);
        
        $entry = new OneGoSDK_DTO_ModifiedCartEntryDto();
        $entry2 = new OneGoSDK_DTO_ModifiedCartEntryDto();
        $cart->add($entry);
        $cart->add($entry2);
        
        $entries = $cart->getEntries();
        $this->assertTrue(count($entries) == 2);
        $this->assertEqual($entries[0], $entry);
        $this->assertEqual($entries[1], $entry2);
    }
    
    public function testLoadEntries()
    {
        $items = array();
        for ($i = 1; $i <= 5; $i++) {
            $entry = new OneGoSDK_DTO_ModifiedCartEntryDto();
            $entry->itemCode = $i;
            $items[] = $this->convertToStdClass($entry);
            $lastEntry = $entry;
        }
        
        $cart = new OneGoSDK_DTO_ModifiedCartDto();
        $cart->loadEntries($items);
        $entries = $cart->getEntries();
        $this->assertTrue(count($entries) == 5);
        $this->assertEqual($entries[4], $lastEntry);
    }
    
    public function testEntryCreate()
    {
        $entry = new OneGoSDK_DTO_ModifiedCartEntryDto();
        $entry->itemCode = 123;
        $entrySimple = $this->convertToStdClass($entry);
        
        $this->assertIsA($entrySimple, 'stdClass');
        $entry = OneGoSDK_DTO_ModifiedCartEntryDto::create($entrySimple);
        $this->assertIsA($entry, 'OneGoSDK_DTO_ModifiedCartEntryDto');
        $this->assertEqual($entry->itemCode, 123);
        
    }
    
    protected function convertToStdClass(OneGoSDK_DTO_ModifiedCartEntryDto $entry)
    {
        $arr = array();
        foreach ($entry as $key => $val) {
            $arr[$key] = $val;
        }
        return (object) $arr;
    }
    
}