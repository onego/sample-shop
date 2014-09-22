<?php

final class OneGoSDK_Log
{
    const INFO  = 'INFO';
    const WARN  = 'WARN';
    const DEBUG = 'DEBUG';

    private static $mark    = 0;
    private static $level   = self::INFO;
    private static $print   = false;
    private static $callback = false;

    private static $levelPriorities = array(
            self::INFO  => 0,
            self::WARN  => 1,
            self::DEBUG => 2,
        );

    public static
    function setLevel($level)
    {
        self::$level    = $level;
    }

    public static
    function setPrint($print)
    {
        self::$print    = (bool) $print;
    }
    
    public static
    function setCallback($callback)
    {
        if (!is_callable($callback)) {
            trigger_error('Parameter is not callable');
            return false;
        }
        self::$callback = $callback;
    }

    public static
    function info($message, $var1 = NULL, $var2 = NULL)
    {
        $args   = func_get_args();
        self::log(self::INFO, $message, array_slice($args, 1));
    }

    public static
    function warn($message, $var1 = NULL, $var2 = NULL)
    {
        $args   = func_get_args();
        self::log(self::WARN, $message, array_slice($args, 1));
    }

    public static
    function debug($message, $var1 = NULL, $var2 = NULL)
    {
        $args   = func_get_args();
        self::log(self::DEBUG, $message, array_slice($args, 1));
    }

    private static
    function log($level, $message, $vars = array())
    {
        if (!self::isLevelCompatible($level))
            return;

        $backtrace  = debug_backtrace();
        $source     = self::callSource($backtrace[2]);
        $message    = "[$level] $source " . vsprintf($message, $vars);

        if (self::$print)
        {
            echo '<pre style="',
                'text-align:left;',
                'background-color:lightgrey;',
                'border:1px solid grey;">',
                    '[mark:', self::$mark++, '] ',
                    date('Y-m-d H:i:s'),
                    "\n",
                    $message,
                '</pre>';
        }
        if (self::$callback) 
        {
            call_user_func(self::$callback, $message, $level);
        }
    }

    private static
    function isLevelCompatible($level)
    {
        return self::$levelPriorities[$level] <=
                self::$levelPriorities[self::$level];
    }

    private static
    function callSource($backtrace)
    {
        return $backtrace['class'] . '::' . $backtrace['function'];
    }
}