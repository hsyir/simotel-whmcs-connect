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
  logActivity(json_encode($_REQUEST), 0);

        $pbx = new Pbx();
        $result = $pbx->dispatchEvent()
            ? ["success" => true]
            : ["success" => false, "errors" => $pbx->errors()];

        $this->echoResponse($result);

    }

    private function echoResponse($result){
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    }

}
