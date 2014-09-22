<?php
class OneGoSDK_DTO_CartEntryDto
{
    const FLAG_IGNORE = 'IGNORE';
    const FLAG_PREPAID_TOPUP = 'PREPAID_TOPUP';

    public $index;
    public $itemCode;
    public $pricePerUnit;
    public $quantity;
    public $cash;

    // optional
    public $groupCode;
    public $itemName;
    public $flags;

    public function setFlag($flag)
    {
        if (empty($this->flags)) {
            $this->flags = array($flag);
        }
    }

    public function unsetFlag($flag)
    {
        $pos = array_search($flag, $this->flags);
        if ($pos !== false) {
            unset($this->flags[$pos]);
        }
        if (empty($this->flags)) {
            $this->flags = null;
        }
    }

    public function unsetFlags()
    {
        $this->flags = null;
    }
}
