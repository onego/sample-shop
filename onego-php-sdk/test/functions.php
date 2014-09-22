<?php

function dim(&$var, $default = NULL)
{
    if (empty($var))
    {
        $var    = $default;
    }

    return $var;
}

function cfg($key)
{
    return Config::get_config_value($key);
}

function dbg()
{
    $args    = func_get_args();
    $isHtml  = (bool) ini_get('html_errors');

    if ($isHtml)
    {
        echo '<pre>';
    }

    mark();

    foreach ($args as $arg)
    {
        var_dump($arg);
        echo "\n";
    }

    if ($isHtml)
    {
        echo '</pre>';
    }
}

function dbge()
{
    $args    = func_get_args();
    call_user_func_array('dbg', $args);
    exit;
}

function mark()
{
    static $mark = 0;
    $trace       = debug_backtrace();

    echo '[mark:', $mark++, '] ';

    if (!empty($trace[1]))
    {
        $callFrom    = $trace[1];

        $root            = explode(DIRECTORY_SEPARATOR, __FILE__);
        $sourceFile      = explode(DIRECTORY_SEPARATOR, $callFrom['file']);
        $relativeFile    = array_diff($sourceFile, $root);

        echo join(DIRECTORY_SEPARATOR, $relativeFile), ':', $callFrom['line'];
    }

    echo "\n";
}