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
        $exten = $_REQUEST["exten"];
        $state = $_REQUEST["state"];
        $uniqueId = $_REQUEST["unique_id"];
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
            "unique_id" => $uniqueId,
        ];

        $notif = new PushNotification();
        $notif->send($channelName, "CallerId", $data);

        header('Content-Type: application/json');
        echo json_encode(["success" => true]);
        exit;
    }

    /**
     * @param string $message
     */
    private function logError(string $message)
    {
        header('Content-Type: application/json');
        echo json_encode(["success" => false,"message"=>$message]);
        exit;
    }

    /**
     * @param $participant
     * @param $exten
     * @param $state
     */
    private function validateApiRequest($participant, $exten, $state, $direction)
    {

        if (!$state) {
            $this->logError("Call state not defined");
            exit;
        }

        if ($state != "Ringing") {
            $this->logError("Only ring state supported");
            exit;
        }

        if ($direction != "in") {
            $this->logError("Only 'in' call direction supported");
            exit;
        }

        if (!$participant) {
            $this->logError("Participant number required");
            exit;
        }

        $config = WhmcsOperations::getConfig();
        if (preg_match($config["BlockPattern"], $participant)) {
            $this->logError("Block number");
            exit;
        }

        if (!$exten) {
            $this->logError("Exten number required");
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
}
