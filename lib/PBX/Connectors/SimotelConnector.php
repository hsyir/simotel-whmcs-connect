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
        list($server, $user, $pass, $context) = $this->serverProfile();
        if (!$server or !$user or !$pass or !$context)
            return $this->addError("اطلاعات تماس با سیموتل کامل نشده است");

        $data = [
            "caller" => WhmcsOperations::getCurrentAdminExten(),
            "callee" => $callee,
            "context" => $context,
            "caller_id" => $callerId,
            "timeout" => "30"
        ];

        $simotelConfig = require __DIR__ . "/simotelConfig.php";
        $simotelConfig['simotelApi']['connect'] = [
            'user' => $user,
            'pass' => $pass,
            'server_address' => $server,
        ];

        $simotel = new Simotel($simotelConfig);

        try {
            $result = $simotel->connect()->call()->originate()->act($data);
            if ($result->success)
                return true;

            $this->addError($result->message);
            return false;

        } catch (\Exception $exception) {
            return $this->addError($exception->getMessage());
        }
    }

    public function callerId($data)
    {

    }

    public function cdr($data)
    {

    }

    /**
     * @return array
     */
    private function serverProfile(): array
    {
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
        return array($server, $user, $pass, $context);
    }
}
