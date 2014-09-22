<?php
interface OneGoSDK_Interface_OneGoOAuth
{
    /**
     * Constructs OneGoAPI client.
     *
     * @param OneGoSDK_Gateway $gateway to access API
     */
    public
    function __construct(OneGoSDK_Impl_OAuthGateway $gateway);

    /**
     * @return OneGoSDK_Config
     */
    public
    function getConfig();
}