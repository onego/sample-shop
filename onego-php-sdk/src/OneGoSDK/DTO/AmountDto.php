<?php

final class OneGoSDK_DTO_AmountDto
{
    public $visible;

    public $precise;

    /**
     * Set amount object from string
     *
     * @param string $visible
     * @return OneGoSDK_DTO_AmountDto 
     */
    public static function asCash($visible)
    {
        $value      = (string) $visible;

        if (!preg_match('/^\d+(\.\d+)?$/', $value))
            throw new OneGoSDK_Exception("Invalid amount given: $value");

        $precise    = preg_replace('/^(\d+\.\d{4}).*$/', '\1', $value);
        $visible    = preg_replace('/^(\d+\.\d{2}).*$/', '\1', $value);

        $amount             = new OneGoSDK_DTO_AmountDto();
        $amount->precise    = $precise;
        $amount->visible    = $visible;
        return $amount;
    }
    
    /**
     *
     * @return float visible value
     */
    public function getVisible()
    {
        return (float) $this->visible;
    }

    /**
     *
     * @return string visible value
     */
    public function __toString()
    {
        return (string) $this->getVisible();
    }

}
