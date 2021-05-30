<?php

namespace WHMCS\Module\Addon\Simotel\PBX\Connectors;

use GuzzleHttp\Client;
use Hsy\Simotel\Simotel;
use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PBX\Errors;
use WHMCS\Module\Addon\Simotel\PBX\PbxConnectorInterface;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class SimotelConnector implements PbxConnectorInterface
{
    use Errors;

    /**
     * @param $caller
     * @param $callee
     * @param $callerId
     * @return mixed
     */
    public function sendCall($caller, $callee, $callerId)
    {


        $configs = WhmcsOperations::getConfig();
        $adminId = WhmcsOperations::getCurrentAdminId();
        $adminOptions = WhmcsOperations::getAdminOptions($adminId);

        $options = new Options();
        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);
        $selectedSimotelProfileName = $adminOptions->simotelProfileName;
        $simotelProfile = (array)collect($simotelServers)->keyBy("profile_name")->get($selectedSimotelProfileName);
        $server = $simotelProfile["server_address"];
        $user = $simotelProfile["api_user"];
        $pass = $simotelProfile["api_pass"];
        $context = $simotelProfile["context"];

        if (!$server or !$user or !$pass or !$context)
            return $this->addError("اطلاعات تماس با سیموتل کامل نشده است");

        $data = [
            "caller" => WhmcsOperations::getCurrentAdminExten(),
            "callee" => $callee,
            "context" => $context,
            "caller_id" => $callerId,
            "timeout" => "30"
        ];

       /* $options = [
            'body' => json_encode($data),
            "headers" => [
                'Content-Type' => ' application/json'
            ],
            'auth' => [
                $user,
                $pass
            ],
            'timeout' => $configs["SimotelResponseTimeout"], // Response timeout
            'connect_timeout' => $configs["SimotelConnectTimeout"], // Connection timeout
        ];*/


        $simotelConfig = require __DIR__ . "/simotelConfig.php";

        $simotelConfig['simotelApi']['connect'] = [
            'user' => $user,
            'pass' => $pass,
            'server_address' => $server ."/api/v3/",

        ];
        $simotel = new Simotel($simotelConfig);
        try{
           $result = $simotel->connect()->call()->originate()->act($data);
           var_dump($result);
           exit;
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
/*
        exit;

        try {
            $client = new Client(["base_uri" => $server]);
            $response = $client->put("/api/v3/call/originate/act", $options);
            return $response->getBody();
        } catch (\Exception $exception) {
            return $this->addError("خطا در ارتباط با سرور سیموتل" . "  " . $exception->getMessage());
        }*/
    }

    public function sendCall2($caller, $callee, $callerId)
    {
        $configs = WhmcsOperations::getConfig();
        $adminId = WhmcsOperations::getCurrentAdminId();
        $adminOptions = WhmcsOperations::getAdminOptions($adminId);

        $options = new Options();
        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);
        $selectedSimotelProfileName = $adminOptions->simotelProfileName;
        $simotelProfile = (array)collect($simotelServers)->keyBy("profile_name")->get($selectedSimotelProfileName);
        $server = $simotelProfile["server_address"];
        $user = $simotelProfile["api_user"];
        $pass = $simotelProfile["api_pass"];
        $context = $simotelProfile["context"];

        if (!$server or !$user or !$pass or !$context)
            return $this->addError("اطلاعات تماس با سیموتل کامل نشده است");

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
            ],
            'timeout' => $configs["SimotelResponseTimeout"], // Response timeout
            'connect_timeout' => $configs["SimotelConnectTimeout"], // Connection timeout
        ];

        try {
            $client = new Client(["base_uri" => $server]);
            $response = $client->put("/api/v3/call/originate/act", $options);
            return $response->getBody();
        } catch (\Exception $exception) {
            return $this->addError("خطا در ارتباط با سرور سیموتل" . "  " . $exception->getMessage());
        }
    }

    public function callerId($data)
    {

    }

    public function cdr($data)
    {

    }
}
