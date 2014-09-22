<?php

class OneGoSDK_Impl_Validator
{
    public static
    function validateRequest($request)
    {
        if (is_null($request))
            throw new OneGoSDK_Exception("Request is not initialized.");

        if (property_exists(get_class($request), 'bimDto'))
            self::validateBim($request->bimDto);
    }

    private static
    function validateBim($bim)
    {
        if (is_null($bim))
            throw new OneGoSDK_Exception("BIM is not initialized.");

        if (empty($bim->bimTypeDto))
            throw new OneGoSDK_Exception("Missing BIM type.");

        if (empty($bim->id))
            throw new OneGoSDK_Exception("Missing BIM ID.");
    }
}