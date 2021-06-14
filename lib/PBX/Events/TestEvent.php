<?php

namespace WHMCS\Module\Addon\Simotel\PBX\Events;

use WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\PushNotification;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class TestEvent extends PbxEvent
{
    /**
     * @return bool
     * @throws \Exception
     */
    public function dispatch(): bool
    {
        $exten = $this->request->exten;
        $participant = $this->request->participant;

        if (!$this->validateNewStateRequest($participant, $exten))
            return false;

        $client = WhmcsOperations::getFirstClientByPhoneNumber($participant);
        $clientData = $client ? $this->extractClientInfo($client) : null;

        $channelName = "private-whmcs-" . $exten;
        $data = [
            "exten" => $exten,
            "participant" => $participant,
            "client" => $clientData,
            "unique_id" => random_int(100000, 999999),
        ];

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
     * @return bool
     */
    private function validateNewStateRequest($participant, $exten): bool
    {

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

        return $this->fails() ? false : true;
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
