<?php

final class OneGoSDK_Simple
{
    public static
    function newWithConfig(OneGoSDK_APIConfig $config)
    {
        return new OneGoSDK_Impl_SimpleAPI(new OneGoSDK_Impl_OneGoAPI(
                new OneGoSDK_Impl_Gateway(
                        $config,
                        new OneGoSDK_Impl_CurlHttpClient()
                    )
            ));
    }

    public static
    function newWithApi(OneGoSDK_Interface_OneGoAPI $api)
    {
        return new OneGoSDK_Impl_SimpleAPI($api);
    }
}