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
        logActivity(json_encode($_REQUEST) . json_encode($_POST), 0);

        $exten = $_REQUEST["exten"];
        $state = $_REQUEST["state"];
        $participant = $_REQUEST["participant"];

        $client = WhmcsOperations::getFirstClientByPhoneNumber($participant);

        if ($client) {
            $clientFullname = $client->firstname . " " . $client->lastname;
            $clientData = [
                "id" => $client->id,
                "firstname" => $client->firstname,
                "lastname" => $client->lastname,
                "fullname" => $clientFullname,
                "companyname" => $client->companyname,
                "notes" => $client->notes,
                "phonenumber" => $client->phonenumber,
            ];
        } else {
            $clientData = null;
        }


        if ($state != "Ringing")
            die("...");

        $channelName = "whmcs" . $exten;
        $data = [
            "exten" => $exten,
            "participant" => $participant,
            "client" => $clientData,
        ];

        $adminId = $this->getCurrentAdminId();

        $notif = new PushNotification();
        $notif->send($channelName, "newCall", $data);
        echo "Done";
        exit;
    }

    private function getCurrentAdminId()
    {
        $command = 'GetAdminDetails';
        $results = localAPI($command);

        if ($results['result'] == 'success') {
            return $results["adminid"];
        } else {
            return null;
        }
    }

}
