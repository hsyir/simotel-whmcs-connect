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
        header('Content-Type: application/json');

        logActivity(json_encode($_REQUEST), 0);
        if ($pbx->dispatchEvent())
            echo json_encode(["success" => true]);
        else {
            logActivity(json_encode($pbx->errors()) . json_encode($_REQUEST), 0);
            echo json_encode(["success" => false, "errors" => $pbx->errors()]);
        }
        exit;

    }

}
