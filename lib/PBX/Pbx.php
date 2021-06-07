<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


use WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\PBX\Connectors\SimotelConnector;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;
use WHMCS\Database\Capsule;

class Pbx
{
    use Errors;

    public function sendCall($callee): bool
    {
        $caller = WhmcsOperations::getCurrentAdminExten();
        $client = WhmcsOperations::getFirstClientByPhoneNumber($callee);
        $callerId = $client ? $client->firstname . " " . $client->lastname : $callee;

        $simotel = new SimotelConnector();
        $simotel->sendCall($caller, $callee, $callerId);
        if ($simotel->fails()) {
            $this->addError($simotel->errors());
            return false;
        }
        return true;
    }

    /**
     * @return bool
     */
    public function newState(): bool
    {
        $exten = $_REQUEST["exten"];
        $adminId = WhmcsOperations::getAdminByExten($exten);
        $state = $_REQUEST["state"];
        $dialing = $_REQUEST["dialing"];
        $uniqueId = $_REQUEST["unique_id"];
        $participant = $_REQUEST["participant"];
        $direction = isset($_REQUEST["direction"]) ? $_REQUEST["direction"] : "in";

        $validate = $this->validateNewStateRequest($participant, $exten, $state, $direction, $dialing);
        if (!$validate)
            return false;

        $client = WhmcsOperations::getFirstClientByPhoneNumber($participant);

        if ($client)
            $clientData = $this->extractClientInfo($client);
        else
            $clientData = null;


        $channelName = "whmcs" . $exten;
        $data = [
            "state" => $state,
            "exten" => $exten,
            "participant" => $participant,
            "client" => $clientData,
            "unique_id" => $uniqueId,
        ];

        try {

            $src = $direction=="in" ? $participant : $exten;
            $dst = $direction=="out" ? $participant : $exten;
            Call::create([
                "unique_id" => $uniqueId,
                "src" => $src,
                "dst" => $dst,
                "client_id" => $client->id,
                "admin_id" => $adminId,
                "status" => $state,
                "direction" => $direction
            ]);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }


        try {
            $notif = new PushNotification();
            $notif->send($channelName, "CallerId", $data);

        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }


        return true;
    }

    public function cdr(): bool
    {
        try {
            $uniqueId = $_REQUEST["unique_id"];
            $call = Call::whereUniqueId($uniqueId)->first();
            if (!$call) {
                $this->addError("Call Data Not Found!");
                return false;
            }
            $call->record = $_REQUEST["record"];
            $call->billsec = $_REQUEST["billsec"];
            $call->status = $_REQUEST["disposition"];
            $call->start_at = $_REQUEST["starttime"];
            $call->end_at = $_REQUEST["endtime"];
            $call->save();

        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
        return true;
    }

    public function dispatchEvent(): bool
    {
        $event = $_REQUEST["event_name"];
        if (!$event) {
            $this->addError("Event name required");
            return false;
        }

        switch ($event) {
            case "Cdr":
                return $this->cdr();

            case "NewState":
                return $this->newState();
        }

        $this->addError("Unknown Event: '$event'");
        return false;
    }

    /**
     * @param $participant
     * @param $exten
     * @param $state
     * @param $direction
     * @param $dialing
     * @return bool
     */
    private function validateNewStateRequest($participant, $exten, $state, $direction, $dialing): bool
    {
        if (!$state)
            $this->addError("Call state not defined");


        if ($direction == "in") {
            if ($state != "Ringing")
                $this->addError("Only 'Ringing' state supported");
        }
        else{
            if ($dialing != "yes") {
                $this->addError("Not Supported State");
            }
        }

        if (!$participant) {
            $this->addError("Participant number required");
        }

        $config = WhmcsOperations::getConfig();
        if (preg_match($config["BlockPattern"], $participant)) {
            $this->addError("Blocked number: '$participant'");
        }

        if (!$exten) {
            $this->addError("Exten number not exists");
        }

        if ($this->fails())
            return false;

        return true;
    }

    /**
     * @param $client
     * @return array
     */
    private function extractClientInfo($client): array
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
