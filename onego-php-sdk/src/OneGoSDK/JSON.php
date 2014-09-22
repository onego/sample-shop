<?php
final class OneGoSDK_JSON
{
    public static function encode($data)
    {
        if (function_exists('json_encode')) {
            return json_encode($data);
        } else {
            $json = new Services_JSON(SERVICES_JSON_IN_OBJ);
            return $json->encode($data);
        }
    }
    
    public static function decode($jsonData)
    {
        if (function_exists('json_decode')) {
            return json_decode($jsonData);
        } else {
            $json = new Services_JSON(SERVICES_JSON_IN_OBJ);
            return $json->decode($jsonData);
        }
    }
}