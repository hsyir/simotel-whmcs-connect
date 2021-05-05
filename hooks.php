<?php
add_hook('AdminAreaPage', 1, function ($vars) {
    $config = \WHMCS\Module\Addon\Simotel\Admin\WhmcsOperations::getConfig();
    $channelName = "whmcs" . \WHMCS\Module\Addon\Simotel\Admin\WhmcsOperations::getCurrentAdminExten();

    $js = <<<EOF

                // Simotel Caller Id 
                window.channelName = "$channelName";
                window.app_key = "$config[WsAppKey]";
                window.app_cluster = "$config[WsCluster]";
                window.auth_endpoint = "$config[AdminWebUrl]/addonmodules.php?module=simotel&action=authorizeChannel";
                window.wsHost = "$config[WsHost]";
                window.wsPort = $config[WsPort];
                window.rootWebUrl = "$config[RootWebUrl]";
                window.panelWebUrl = "$config[AdminWebUrl]";
                
                
                var pusherScript = document.createElement('script');
                pusherScript.onload = function () {
                    var simotelScript = document.createElement('script');
                    simotelScript.src = "$config[RootWebUrl]/modules/addons/simotel/templates/js/simotel.js";
                    document.head.appendChild(simotelScript); //or something of th
                };
                pusherScript.src = "https://js.pusher.com/7.0.0/pusher.min.js";
                document.head.appendChild(pusherScript); //or something of th
            

EOF;

    $extraVariables = [];
    $extraVariables['jscode'] =
        $vars["jscode"] . $js;
    return $extraVariables;
});
