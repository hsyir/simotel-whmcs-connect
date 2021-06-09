<?php
namespace WHMCS\Module\Addon\Simotel\PBX\Events;

use WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class NewState extends PbxEvent
{
    /**
     * @return bool
     */
    public function dispatch(): bool
    {
        $exten = $this->request->exten;
        $adminId = WhmcsOperations::getAdminByExten($exten);
        $state = $this->request->state;
        $dialing = $this->request->dialing;
        $uniqueId = $this->request->unique_id;
        $participant = $this->request->participant;
        $direction = isset($this->request->direction) ? $this->request->direction : "in";

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

        if($uniqueId){
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
        } else {
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
