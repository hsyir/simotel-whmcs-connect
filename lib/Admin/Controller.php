<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


/**
 * Sample Admin Area Controller
 */
class Controller
{

    public function index($vars)
    {

/*
        $clients = WhmcsOperations::getClientsByPhoneNumber("09370331680");
        echo "<pre>" . var_export($clients,true)."</pre>";
        exit;*/


        $adminExten = WhmcsOperations::getCurrentAdminExten();

        $channelName = "whmcs$adminExten";

        return WhmcsOperations::render("adminIndexPage", compact("channelName"));
    }

    public function authorizeChannel()
    {
        echo (new PushNotification([]))->authorize("whmcsChannel");
        exit;
    }

}
