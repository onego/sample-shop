<?php

final class OneGoSDK_DTO_DiscountDto
{
    public $amount;
    public $percent;
    
    /**
     *
     * @return float amount visible 
     */
    public function getAmount()
    {
        return is_null($this->amount) ? null : OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_AmountDto', $this->amount);
    }
    
    /**
     *
     * @return float amount percent
     */
    public function getPercents()
    {
        return isset($this->percent) ? (float) $this->percent : null;
    }
}
