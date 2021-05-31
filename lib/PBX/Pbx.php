<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


use WHMCS\Module\Addon\Simotel\PBX\Connectors\SimotelConnector;
use WHMCS\Module\Addon\Simotel\WhmcsOperations;

/**
 *
 */
class Pbx
{
    use Errors;

    public function sendCall($callee)
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
