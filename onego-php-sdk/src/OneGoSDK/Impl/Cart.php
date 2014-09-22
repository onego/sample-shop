<?php
class OneGoSDK_Impl_Cart implements Countable
{
    protected $cartEntries = array();
    
    /**
     *
     * @return array Cart entries
     */
    public function getEntries()
    {
        return array_values($this->cartEntries);
    }
    
    /**
     *
     * @param string $itemCode
     * @param float $pricePerUnit
     * @param int $quantity
     * @param float $cashAmount
     * @param string $itemName
     * @param string $groupCode
     * @param boolean $ignore
     * @return OneGoSDK_Impl_Cart 
     */
    public function addEntry($itemCode, $pricePerUnit, $quantity = 1, 
        $cashAmount = false, $itemName = false, $groupCode = false, $ignore = false, $topup = false)
    {
        $idx = count($this->cartEntries);
        $this->setEntry($idx, $itemCode, $pricePerUnit, $quantity, $cashAmount, 
            $itemName, $groupCode, $ignore, $topup
        );
        return $this;
    }
    
    /**
     *
     * @param string $index
     * @param string $itemCode
     * @param float $pricePerUnit
     * @param int $quantity
     * @param float $cashAmount
     * @param string $itemName
     * @param string $groupCode
     * @param boolean $ignore
     * @return OneGoSDK_Impl_Cart 
     */
    public function setEntry($index, $itemCode, $pricePerUnit, $quantity = 1, 
        $cashAmount = false, $itemName = false, $groupCode = false, $ignore = false, $topup = false)
    {
        $entry = new OneGoSDK_DTO_CartEntryDto();
        $entry->index = $index;
        $entry->itemCode = $itemCode;
        $entry->pricePerUnit = $pricePerUnit;
        $entry->quantity = $quantity;
        $entry->cash = $cashAmount === false ? $pricePerUnit * $quantity : $cashAmount;
        if ($groupCode !== false) {
            $entry->groupCode = $groupCode;
        }
        if (!empty($itemName)) {
            $entry->itemName = $itemName;
        }
        if ($ignore) {
            $entry->setFlag(OneGoSDK_DTO_CartEntryDto::FLAG_IGNORE);
        }
        if ($topup) {
            $entry->setFlag(OneGoSDK_DTO_CartEntryDto::FLAG_PREPAID_TOPUP);
        }
        $this->cartEntries[$index] = $entry;
        return $this;
    }
    
    /**
     * Empty cart
     */
    public function removeAll()
    {
        $this->cartEntries = array();
    }
    
    /**
     *
     * @return int Cart items count 
     */
    public function count()
    {
        return count($this->cartEntries);
    }
}
