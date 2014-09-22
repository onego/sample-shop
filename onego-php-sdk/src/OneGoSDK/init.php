<?php

function OneGoSDK_Autoload($className)
{
    list ($prefix, $name) = explode('_', $className, 2) + array(NULL, NULL);

    if ('OneGoSDK' == $prefix)
        $className  = $name;

    if (!$name)
        $className  = $prefix;

    $includePaths   = explode(PATH_SEPARATOR, get_include_path());
    array_unshift($includePaths, dirname(__FILE__) . '/Vendors');
    array_unshift($includePaths, dirname(__FILE__));

    $includeFile    = str_replace('_', '/', $className) . '.php';
    $found          = false;

    foreach ($includePaths as $includePath)
    {
        if (file_exists($file = "$includePath/$includeFile"))
        {
            require $file;
            return true;
        }
    }

    return false;
}

spl_autoload_register('OneGoSDK_Autoload');
OneGoSDK_Autoload('OneGoSDK_Exception');