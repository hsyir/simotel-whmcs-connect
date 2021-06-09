<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


use WHMCS\Module\Addon\Simotel\PBX\Connectors\SimotelConnector;
use WHMCS\Module\Addon\Simotel\PBX\Events\Cdr;
use WHMCS\Module\Addon\Simotel\PBX\Events\NewState;
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

    public function dispatchEvent(): bool
    {
        $eventName = $_REQUEST["event_name"];
        if (!$eventName) {
            $this->addError("Event name required");
            return false;
        }

        switch ($eventName) {
            case "Cdr":
                $event = new Cdr;
                break;
            case "NewState":
                $event = new NewState;
                break;
            default:
                $this->addError("Unknown Event: '$eventName'");
                return false;
        }

        $event->dispatch();

        if (!$event->fails())
            return true;

        $this->addError($event->errors());
        return false;

    }

}
