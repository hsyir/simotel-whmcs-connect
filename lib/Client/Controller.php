<?php

namespace WHMCS\Module\Addon\Simotel\Client;

use GuzzleHttp\Client;
use WHMCS\Module\Addon\Simotel\Options;
use WHMCS\Module\Addon\Simotel\PBX\Pbx;
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
        $pbx = new Pbx();
        header('Content-Type: application/json');

        if ($pbx->dispatchEvent())
            echo json_encode(["success" => true]);
        else {
//            logActivity(json_encode($pbx->errors()), 0);
            echo json_encode(["success" => false, "errors" => $pbx->errors()]);
        }
        exit;

        /*
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
        exit;*/
    }

}
