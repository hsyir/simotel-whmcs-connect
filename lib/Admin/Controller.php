<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 * Admin Area Controller
 */
class Controller
{

    public function index($vars)
    {
        $options = new Options();

        $adminId = WhmcsOperations::getCurrentAdminId();
        $selectedSimotelProfileName = $options->get("simotelProfile",$adminId);

        $simotelServerProfiles = $options->get("simotelServerProfiles");

        $simotelServers = json_decode($simotelServerProfiles);

        return WhmcsOperations::render("adminIndexPage",compact("simotelServers",'selectedSimotelProfileName'));
    }

    public function authorizeChannel()
    {
        echo (new PushNotification([]))->authorize("whmcsChannel");
        exit;
    }

    public function storeMyConfigs()
    {
        $adminId = WhmcsOperations::getCurrentAdminId();
        $simotelProfileName = $_REQUEST["simotel_profile"];
        $exten = $_REQUEST["exten"];
        $options = new Options();
        $options->set("exten", $exten, $adminId);
        $options->set("simotelProfile", $simotelProfileName, $adminId);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }

    public function moduleConfigForm()
    {
        if(!WhmcsOperations::adminCanConfigureModuleConfigs())
            return "Unauthorized";

        $options = new Options();
        $simotelServers = $options->get("simotelServerProfiles", null, []);
        $simotelServers = json_decode($simotelServers);

        return WhmcsOperations::render("moduleConfigs", compact("simotelServers"));
    }

    public function storeModuleConfigs()
    {
        $profiles = $_REQUEST["simotelServerProfile"];
        $profiles = json_encode($profiles);

        $options = new Options();
        $options->set("simotelServerProfiles", $profiles);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }


}
