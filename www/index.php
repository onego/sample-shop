<?php

require_once __DIR__ . '/../onego-php-sdk/src/OneGoSDK/init.php';
require_once __DIR__ . '/../src/SampleShop.php';


$action = @$_GET['a'] ?: 'main';
if (method_exists('SampleShop', $action)) {
    try {
        $shop = new SampleShop();
        $shop->$action();
    } catch (Exception $e) {
        error_log($e);
        exit(get_class($e) . ": " . $e->getMessage());
    }
} else {
    header($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
    exit("Not Found");
}
