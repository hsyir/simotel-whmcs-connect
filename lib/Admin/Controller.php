<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 * Admin Area Controller
 */
class Controller
{

    public function index($vars)
    {

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
