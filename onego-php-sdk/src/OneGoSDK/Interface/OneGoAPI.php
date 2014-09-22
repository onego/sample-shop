<?php

interface OneGoSDK_Interface_OneGoAPI
{
    /**
     * Constructs OneGoAPI client.
     *
     * @param OneGoSDK_Gateway $gateway to access API
     */
    public function __construct(OneGoSDK_Interface_Gateway $gateway);

    /**
     * @return OneGoSDK_Config
     */
    public function getConfig();
}