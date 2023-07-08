<?php

if (!function_exists("baseUrl")) {
    function baseUrl($url = null): string
    {
        return
            \WHMCS\Utility\Environment\WebHelper::getBaseUrl()
            . $url;
    }
}

if (!function_exists("templateUrl")) {
    function templateUrl($url = null): string
    {
        return baseUrl("/modules/addons/simotel/templates" . $url);
    }
}

if (!function_exists("adminUrl")) {
    function adminUrl($url = null): string
    {
        $config = WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
        $customAdminPath = $config["CustomAdminPath"] ?? "";

        return baseUrl($customAdminPath . $url);

    }
}


if (!function_exists("dd")) {
    function dd($var): string
    {
        echo "<pre>" . var_export($var, true) . "</pre>";
        exit;
    }
}
