<?php

final class OneGoSDK
{
    public static
    function amount($amount)
    {
        return OneGoSDK_DTO_AmountDto::asCash($amount);
    }
}