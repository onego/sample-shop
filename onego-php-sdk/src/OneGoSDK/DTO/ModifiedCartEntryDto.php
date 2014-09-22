<?php
class OneGoSDK_DTO_ModifiedCartEntryDto extends OneGoSDK_DTO_CartEntryDto
{
    // optional
    public $discount;
    public $prepaidReceived;
    
    /**
     * Transform from stdClass
     *
     * @param stdClass $object
     * @return OneGoSDK_DTO_ModifiedCartEntryDto Transformed object 
     */
    public static function create(stdClass $object)
    {
        return OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_ModifiedCartEntryDto', $object);
    }
    
    /**
     *
     * @return OneGoSDK_DTO_DiscountDto 
     */
    public function getDiscount()
    {
        $discount = $this->discount;
        if (!empty($discount)) {
            $discount = OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_DiscountDto', $discount);
        }
        return $discount;
    }
    
    /**
     *
     * @return string 
     */
    public function getPrepaidReceived()
    {
        return $this->prepaidReceived;
    }
}
