<?php

namespace WHMCS\Module\Addon\Simotel\Client;

use GuzzleHttp\Client;
use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;
use WHMCS\Database\Capsule;

/**
 * Sample Client Area Controller
 */
class Controller
{
    public function index($vars)
    {

        logActivity(json_encode($_REQUEST), 0);
        $exten = $_REQUEST["exten"];
        $state = $_REQUEST["state"];
        $participant = $_REQUEST["participant"];
        $direction = isset($_REQUEST["direction"]) ? $_REQUEST["direction"] : "in";

        $this->validateApiRequest($participant, $exten, $state, $direction);

        $client = WhmcsOperations::getFirstClientByPhoneNumber($participant);

        if ($client) {
            $clientData = $this->extractClientResource($client);
        } else {
            $clientData = null;
        }

        $channelName = "whmcs" . $exten;
        $data = [
            "state" => $state,
            "exten" => $exten,
            "participant" => $participant,
            "client" => $clientData,
        ];

        $notif = new PushNotification();
        $notif->send($channelName, "newCall", $data);

        echo "Call data successfully sent to client";
        exit;
    }

    /**
     * @param string $string
     */
    private function logError(string $string)
    {
        echo $string;
        logActivity("Simotel Error: " . $string . "  " . json_encode($_REQUEST), 0);
    }

    /**
     * @param $participant
     * @param $exten
     * @param $state
     */
    private function validateApiRequest($participant, $exten, $state, $direction)
    {

        if (!$state) {
            $this->logError("call state not defined");
            exit;
        }

        if ($state != "Ringing") {
            $this->logError("only ring state supported");
            exit;
        }

        if ($direction != "in") {
            $this->logError("only 'in' call direction supported");
            exit;
        }

        if (!$participant) {
            $this->logError("participant number required");
            exit;
        }

        $config = WhmcsOperations::getConfig();
        var_dump(preg_match($config["BlockPattern"], $participant));
        echo $config["BlockPattern"] . "<br>";
        if (preg_match($config["BlockPattern"], $participant)) {
            $this->logError("Block number");
            exit;
        }

        if (!$exten) {
            $this->logError("exten number required");
            exit;
        }

    }

    /**
     * @param $client
     * @return array
     */
    private function extractClientResource($client): array
    {
        $clientFullname = $client->firstname . " " . $client->lastname;
        return [
            "id" => $client->id,
            "firstname" => $client->firstname,
            "lastname" => $client->lastname,
            "fullname" => $clientFullname,
            "companyname" => $client->companyname,
            "notes" => $client->notes,
            "phonenumber" => $client->phonenumber,
        ];
    }

    public function simotelCall($vars)
    {
        $options = new Options();
        $config = WhmcsOperations::getConfig();
        $adminId = WhmcsOperations::getCurrentAdminId();
        $selectedSimotelProfileName = $options->get("simotelProfile",$adminId);

        $simotelServerProfiles = $options->get("simotelServerProfiles");
        $simotelServers = json_decode($simotelServerProfiles);

        $simotelProfile = (array) collect($simotelServers)->keyBy("profile_name")->get($selectedSimotelProfileName);

        $server = $simotelProfile["server_address"];
        $user = $simotelProfile["api_user"];
        $pass = $simotelProfile["api_pass"];
        $context = $simotelProfile["context"];


        $callee = $_REQUEST["callee"];
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

        $client = new Client(["base_uri" => $server]);
        $response = $client->put("/api/v3/call/originate/act", $options);

        header('Content-Type: application/json');
        echo $response->getBody();;
        exit;
    }


    public function test()
    {
        WhmcsOperations::adminCanConfigureModuleConfigs();
    }
}
