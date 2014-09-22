<?php
final class OneGoSDK_DTO_FundsReceivedDto
{
    public $amount = true;
    public $validFrom;
    public $validTo;
    
    /**
     * @return OneGoSDK_DTO_AmountDto amount
     */
    public function getAmount()
    {
        return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_AmountDto', $this->amount);
    }

    /**
     *
     * @return boolean True if prepaid received has pending period
     */
    public function isPending()
    {
        return !empty($this->validFrom);
    }

    /**
     *
     * @return mixed UNIX timestamp or false if prepaid is not pending
     */
    public function getPendingUntil()
    {
        return $this->isPending() ? ceil($this->validFrom / 1000) : false;
    }
}
