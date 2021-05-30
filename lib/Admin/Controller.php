<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use GuzzleHttp\Client;
use Hsy\Simotel\Simotel;
use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PBX\Pbx;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\Smarty;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 * Admin Area Controller
 */
class Controller
{

    public function index($vars)
    {
        $adminId = WhmcsOperations::getCurrentAdminId();

        $options = new Options();
        $adminOptions = $options->getAdminOptions($adminId);
        $popUpButtons = $adminOptions->selectedPopUpButtons;

        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);

        return Smarty::render("adminUserOptions", compact("simotelServers", 'adminOptions', 'popUpButtons'));
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
        $callerIdPopUpActive = $_REQUEST["caller_id_pop_up"] == "on";
        $clickToDialActive = $_REQUEST["click_to_dial"] == "on";

        $popUpButtons = $_REQUEST["popup_buttons"] ?? [];
        $selectedPopUpButtons = [];
        foreach ($popUpButtons as $btn => $status) {
            $selectedPopUpButtons[$btn] = true;
        }

        $optionValues = compact("exten", "callerIdPopUpActive", "simotelProfileName", "clickToDialActive", "selectedPopUpButtons");

        $options = new Options();
        $options->setAdminOptions($adminId, $optionValues);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }

    public function moduleConfigForm()
    {
        if (!WhmcsOperations::adminCanConfigureModuleConfigs())
            return "Unauthorized";

        $options = new Options();
        $simotelServers = $options->get("simotelServerProfiles", null, []);
        $simotelServers = json_decode($simotelServers);

        return Smarty::render("moduleConfigs", compact("simotelServers"));
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



    // --------------------------------------------------------------------
    // ---- Click To Dial --------------------------------------------------
    // --------------------------------------------------------------------
    public function simotelCall($vars)
    {
        $callee = $_REQUEST["callee"];
        if (!$callee) $this->returnCallError("شماره مقصد نامشخص است");

        $pbx = new Pbx();
        $result = $pbx->sendCall($callee);


        header('Content-Type: application/json');
        if ($pbx->fails()) {
            echo json_encode(["success" => false, "message" => $pbx->errors()]);
            exit;
        }

        header('Content-Type: application/json');
        echo $result;
        exit;
    }

    private function returnCallError($message)
    {
    }

}
