<?php

namespace WHMCS\Module\Addon\Simotel\PBX;


/**
 *
 */
interface PbxConnectorInterface
{
    public function sendCall($caller, $callee, $callerId);

    public function callerId($data);

    public function cdr($data);

    public function fails();

    public function errors();
}
