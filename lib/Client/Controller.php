<?php

namespace WHMCS\Module\Addon\Simotel\Client;

use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 * Sample Client Area Controller
 */
class Controller
{
    public function index($vars)
    {
        $exten = $_REQUEST["exten"];
        $state = $_REQUEST["state"];
        $participant = $_REQUEST["participant"];

        $this->validetApiRequest($participant, $exten, $state);

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
        logActivity("Simotel Error: " . $string . "  " . json_encode($_REQUEST), 0);
    }

    /**
     * @param $participant
     * @param $exten
     * @param $state
     */
    private function validetApiRequest($participant, $exten, $state)
    {

        if (!$state) {
            $this->logError("call state not defined");
            exit;
        }

        if ($state != "Ringing") {
            $this->logError("only ring state supported");
            exit;
        }

        if (!$participant) {
            $this->logError("participant number required");
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

}
