<?php
add_hook('AdminAreaPage', 1, function ($vars) {
    $config = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
    $adminOptions = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminOptions();
    $channelName = "whmcs" . \WHMCS\Module\Addon\Simotel\WhmcsOperations::getCurrentAdminExten();


    $popUpTime = isset($config["PopUpTime"]) ? $config["PopUpTime"] : "30";
    $phoneNumberRegx = isset($config["PhoneNumberRegx"]) ? $config["PhoneNumberRegx"] : "/09[0-9]{9}/g";
    $clickToDialActive = $adminOptions->clickToDialActive === true ? "true" : "false";
    $callerIdPopUpActive = $adminOptions->callerIdPopUpActive === true ? "true" : "false";

    $popUpButtons = collect($adminOptions->selectedPopUpButtons)->keys();
    if($popUpButtons==[])
        $popUpButtons=["view_profile","notes","tickets", "create_ticket"];
    $selectedPupUpButtons = json_encode($popUpButtons);

    $js = <<<EOF

                window.channelName = "$channelName";
                window.app_key = "$config[WsAppKey]";
                window.app_cluster = "$config[WsCluster]";
                window.auth_endpoint = "$config[AdminWebUrl]/addonmodules.php?module=simotel&action=authorizeChannel";
                window.wsHost = "$config[WsHost]";
                window.wsPort = "$config[WsPort]";
                window.rootWebUrl = "$config[RootWebUrl]";
                window.panelWebUrl = "$config[AdminWebUrl]";
                window.addonUrl = "$config[RootWebUrl]"+"/modules/addons/simotel";
                window.popUpTime = $popUpTime ;
                window.phoneNumberRegx = $phoneNumberRegx ;
                window.callerIdPopUpActive = $callerIdPopUpActive ;
                window.clickToDialActive = $clickToDialActive ;
                window.selectedPopUpButtons = {$selectedPupUpButtons} ;
                
                var pusherScript = document.createElement('script');
                pusherScript.onload = function () {
                    var simotelScript = document.createElement('script');
                    simotelScript.src = "$config[RootWebUrl]/modules/addons/simotel/templates/js/simotel.min.js";
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
