<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


use WHMCS\Module\Addon\Simotel\PBX\Connectors\SimotelConnector;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class Pbx
{
    use Errors;

    public function dispatchEvent(): bool
    {
        $eventName = $_REQUEST["event_name"];
        $eventClass = '\\WHMCS\\Module\\Addon\\Simotel\\PBX\\Events\\' . $eventName;
        if (!class_exists($eventClass)) {
            $this->addError("Listener for '$eventName' event not found");
            return false;
        }

        $event = new $eventClass;
        $event->dispatch();
        if (!$event->fails())
            return true;

        $this->addError($event->errors());
        return false;

    }

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


}
