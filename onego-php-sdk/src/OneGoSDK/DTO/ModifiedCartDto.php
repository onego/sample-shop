<?php
final class OneGoSDK_DTO_ModifiedCartDto
{
    public $originalAmount;
    public $cashAmount;
    public $payableAmount;
    public $entryDiscount;
    public $cartDiscount;
    public $totalDiscount;
    public $prepaidSpent;
    public $prepaidTopup;
    public $prepaidReceived;
    public $entries = array();
    
    /**
     * Add entry to cart
     *
     * @param OneGoSDK_DTO_ModifiedCartEntryDto $entry
     * @return OneGoSDK_DTO_ModifiedCartDto self
     */
    public function add(OneGoSDK_DTO_ModifiedCartEntryDto $entry)
    {
        $this->entries[] = $entry;
        return $this;
    }
    
    /**
     * Load entries from array
     *
     * @param array $entries
     * @return OneGoSDK_DTO_ModifiedCartDto 
     */
    public function loadEntries($entries = array())
    {
        foreach ($entries as $entry) {
            $this->add(OneGoSDK_DTO_ModifiedCartEntryDto::create($entry));
        }
        return $this;
    }
    
    /**
     *
     * @return array Cart entries
     */
    public function getEntries()
    {
        if (!empty($this->entries)) {
            foreach ($this->entries as $key => $entry) {
                $this->entries[$key] = OneGoSDK_Impl_Transform::transform('OneGoSDK_DTO_ModifiedCartEntryDto', $entry);
            }
        }
        return $this->entries;
    }
    
    /**
     *
     * @param string $methodName
     * @param mixed $methodArguments
     * @return mixed Property value or transformed property object 
     */
    public function __call($methodName, $methodArguments) {
        if (substr($methodName, 0, 3) == 'get') {
            switch ($methodName) {
                case 'getOriginalAmount':
                case 'getCashAmount':
                case 'getPayableAmount':
                    $className = 'OneGoSDK_DTO_AmountDto';
                    break;
                case 'getEntryDiscount':
                case 'getCartDiscount':
                case 'getTotalDiscount':
                    $className = 'OneGoSDK_DTO_DiscountDto';
                    break;
                case 'getPrepaidReceived':
                    $className = 'OneGoSDK_DTO_FundsReceivedDto';
                    break;
            }
            
            $propertyName = $this->resolvePropertyName($methodName);
            if (isset($this->$propertyName)) {
                if (!empty($className)) {
                    if (empty($this->$propertyName)) {
                        return null;
                    } else {
                        return OneGoSDK_Impl_Transform::transform($className, $this->$propertyName);
                    }
                } else {
                    return $this->$propertyName;
                }
            }
            return null;
        }
        throw new Exception('Method '.$methodName.' is undefined');
    }
    
    /**
     *
     * @param string $getterMethodName
     * @return string Property name
     */
    private function resolvePropertyName($getterMethodName)
    {
        $fieldName = substr($getterMethodName, 3);
        $fieldName = strtolower($fieldName[0]) . substr($fieldName, 1);
        return $fieldName;
    }
}
