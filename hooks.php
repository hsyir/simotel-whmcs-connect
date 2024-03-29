<?php


add_hook('AdminAreaPage', 1, function ($vars) {
    $config = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
    $version = $config["version"];
    $adminOptions = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminOptions();
    $channelName = "private-whmcs-" . \WHMCS\Module\Addon\Simotel\WhmcsOperations::getCurrentAdminExten();

    $popUpTime = isset($config["PopUpTime"]) ? $config["PopUpTime"] : "30";
    $phoneNumberRegx = isset($config["PhoneNumberRegx"]) ? $config["PhoneNumberRegx"] : "/09[0-9]{9}/g";
    $clickToDialActive = $adminOptions->clickToDialActive === true ? "true" : "false";
    $callerIdPopUpActive = $adminOptions->callerIdPopUpActive === true ? "true" : "false";

    $popUpButtons = collect($adminOptions->selectedPopUpButtons)->keys();
    if ($popUpButtons == [])
        $popUpButtons = ["view_profile", "notes", "tickets", "create_ticket"];
    $selectedPupUpButtons = json_encode($popUpButtons);

    $js = <<<EOF
                window.channelName = "$channelName";
                window.SimotelConnectVersion = "$version";
                window.app_key = "$config[WsAppKey]";
                window.app_cluster = "$config[WsCluster]";
                window.wsHost = "$config[WsHost]";
                window.wsPort = "$config[WsPort]";
                window.rootWebUrl = whmcsBaseUrl ;
                window.panelWebUrl = whmcsBaseUrl + adminBaseRoutePath ;
                window.addonUrl = whmcsBaseUrl + "/modules/addons/simotel";
                window.authEndpoint = window.panelWebUrl + "/addonmodules.php?module=simotel&action=authorizeChannel";
                window.popUpTime = $popUpTime ;
                window.phoneNumberRegx = $phoneNumberRegx ;
                window.callerIdPopUpActive = $callerIdPopUpActive ;
                window.clickToDialActive = $clickToDialActive ;
                window.selectedPopUpButtons = {$selectedPupUpButtons} ;
                
                var pusherScript = document.createElement('script');
                pusherScript.onload = function () {
                    var simotelScript = document.createElement('script');
                    simotelScript.src = addonUrl +"/templates/js/simotel.js?ver={$version}";
                    document.head.appendChild(simotelScript); //or something of th
                };
                pusherScript.src = "https://js.pusher.com/7.0.0/pusher.min.js";
                document.head.appendChild(pusherScript); //or something of th
EOF;

    $extraVariables = [];
    $extraVariables['jscode'] = $vars["jscode"] . $js;
    return $extraVariables;
});
/*add_hook('AdminAreaClientSummaryPage', 1, function ($vars) {
    return 'This message will be output on the Client Summary page. I can add HTML here';
});*/

add_hook('AdminAreaClientSummaryActionLinks', 1, function ($vars) {
    $config = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getConfig();
    $cdrLink = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getAdminPanelUrl("/addonmodules.php?module=simotel&action=cdrReport&client_id=$vars[userid]");
    $return = [];
    $icon = \WHMCS\Module\Addon\Simotel\WhmcsOperations::getRootUrl("/modules/addons/simotel/templates/images/call-history-icon.png");
    $return[] = '<a href="' . $cdrLink . '">
                    <img src="' . $icon . '" border="0" align="absmiddle">
                    آخرین تماس ها
                    </a>';

    return $return;
});


add_hook('AdminHomeWidgets', 1, function() {

    require_once(__DIR__ . "/vendor/autoload.php");
    return new WHMCS\Module\Addon\Simotel\Widgets\HomePageWidget();
});
