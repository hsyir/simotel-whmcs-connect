<?php

namespace WHMCS\Module\Addon\Simotel\Client;

use WHMCS\Module\Addon\Simotel\PBX\Pbx;

/**
 * Sample Client Area Controller
 */
class Controller
{
    public function index($vars)
    {
        $pbx = new Pbx();
        $json = $pbx->dispatchEvent()
            ? json_encode(["success" => true])
            : json_encode(["success" => false, "errors" => $pbx->errors()]);

        $this->echoResponse($json);

    }

    private function echoResponse($json){
        header('Content-Type: application/json');
        echo $json;
        exit;
    }

}
