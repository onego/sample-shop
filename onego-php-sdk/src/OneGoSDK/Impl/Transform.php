<?php

class OneGoSDK_Impl_Transform
{
    /**
     * Transforms given object to object of given class.
     *
     * @param string $class to transform to
     * @param stdClass $source to transform from
     * @param string Error DTO class name, 'OneGoSDK_DTO_ErrorDto' by default
     * @return object of type $class
     * @throws OneGoSDK_Exception
     */
    public static
    function transform($class, $source, $errorDTOClass = 'OneGoSDK_DTO_ErrorDto')
    {   
        if (get_class($source) == $class)
        {
            return $source;
        }

        $obj = new $class();

        foreach ($obj as $field => $value)
        {
            if (!property_exists($source, $field) && !is_null($value))
            {
                if ($class != $errorDTOClass)
                {
                    $error = self::transform(
                        $errorDTOClass,
                        $source
                    );
                    throw OneGoSDK_Exception::fromError($error);
                }

                throw new OneGoSDK_Exception(
                    "Invalid source (".get_class($source).") to cast to $class, missing property \"{$field}\": "
                        . var_export($source, true));
            }

            if (property_exists($source, $field)) {
                $obj->{$field} = $source->{$field};
            }
        }

        return $obj;
    }
}