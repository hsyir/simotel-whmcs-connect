<?php

namespace WHMCS\Module\Addon\Simotel\Client;

use WHMCS\Module\Addon\Simotel\Admin\PushNotification;
use WHMCS\Module\Addon\Simotel\Admin\WhmcsOperations;

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

        $client = WhmcsOperations::getFirstClientByPhoneNumber($participant);

        /*
                echo "<pre>".var_export($client,true) . "</pre>";
                exit;*/
        $clientFullname = $client->firstname . " " . $client->lastname .  ($client->companyname!="" ? " (" .$client->companyname . ")" : "" );
        $clientData=[];
        if($client){
            $clientData =[
                "id" => $client->id,
                "firstname" => $client->firstname,
                "lastname" => $client->lastname,
                "fullname" => $clientFullname,
                "notes" => $client->notes,
                "phonenumber" => $client->phonenumber,
            ];
        }


        if ($state != "Ringing")
            die("...");




        $channelName = "whmcs" . $exten;
        $data = [
            "exten"=>$exten,
            "participant"=>$participant,
            "client_id"=>$client->id,
            "client"=>$clientData,
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
