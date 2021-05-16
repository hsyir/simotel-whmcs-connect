<?php

namespace WHMCS\Module\Addon\Simotel\Admin;


use GuzzleHttp\Client;
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



    // --------------------------------------------------------------------
    // ---- Simotel Call --------------------------------------------------
    // --------------------------------------------------------------------
    public function simotelCall($vars)
    {
        $options = new Options();
        $adminId = WhmcsOperations::getCurrentAdminId();

        $selectedSimotelProfileName = $options->get("simotelProfile", $adminId);
        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);
        $simotelProfile = (array)collect($simotelServers)->keyBy("profile_name")->get($selectedSimotelProfileName);

        $server = $simotelProfile["server_address"];
        $user = $simotelProfile["api_user"];
        $pass = $simotelProfile["api_pass"];
        $context = $simotelProfile["context"];

        if(!$server or !$user or !$pass or !$context)
            $this->returnCallError("اطلاعات تماس با سیموتل کامل نشده است");

        $callee = $_REQUEST["callee"];
        if(!$callee) $this->returnCallError("شماره مقصد نامشخص است");

        $client = WhmcsOperations::getFirstClientByPhoneNumber($callee);
        $callerId = $client ? $client->firstname . " " . $client->lastname : $callee;

        $data = [
            "caller" => WhmcsOperations::getCurrentAdminExten(),
            "callee" => $callee,
            "context" => $context,
            "caller_id" => $callerId,
            "timeout" => "30"
        ];

        $options = [
            'body' => json_encode($data),
            "headers" => [
                'Content-Type' => ' application/json'
            ],
            'auth' => [
                $user,
                $pass
            ]
        ];

        try {
            $client = new Client(["base_uri" => $server]);
            $response = $client->put("/api/v3/call/originate/act", $options);
        }
        catch (\Exception $exception){
            $this->returnCallError("خطا در ارتباط با سرور سیموتل");
        }

        header('Content-Type: application/json');
        echo $response->getBody();;
        exit;
    }

    private function returnCallError($message)
    {
        header('Content-Type: application/json');
        echo json_encode(["success"=>false,"message"=>$message]);
        exit;
    }

}
