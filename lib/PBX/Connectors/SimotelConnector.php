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
            if ($result->getStatusCode() == 200)
                return true;

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
        $options = new Options();
        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);
        $selectedSimotelProfileName = WhmcsOperations::getCurrentAdminServerProfile();
        $simotelProfile = (array)collect($simotelServers)->keyBy("profile_name")->get($selectedSimotelProfileName);
        $server = $simotelProfile["server_address"];
        $server .= "/api/v3/";
        $user = $simotelProfile["api_user"];
        $pass = $simotelProfile["api_pass"];
        $context = $simotelProfile["context"];
        return array($server, $user, $pass, $context);
    }


    public function downloadAudio($filename)
    {
        list($server, $user, $pass, $context) = $this->serverProfile();
        if (!$server or !$user or !$pass or !$context)
            return $this->addError("اطلاعات تماس با سیموتل کامل نشده است");

        $data = [
            "file" => $filename,
        ];

        $simotelConfig = require __DIR__ . "/simotelConfig.php";
        $simotelConfig['simotelApi']['connect'] = [
            'user' => $user,
            'pass' => $pass,
            'server_address' => $server,
        ];

        $simotel = new Simotel($simotelConfig);

        try {
            $result = $simotel->connect()->reports()->audio()->download($data);

            if ($result->getStatusCode() != 200)
                return false;
            header('Content-type: audio/mp3');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            echo $result->getBody()->getContents();
            exit;

        } catch (\Exception $exception) {
            WhmcsOperations::dd("error");
            WhmcsOperations::dd($exception->getMessage());
            exit;

            return $this->addError($exception->getMessage());
        }
    }
}
