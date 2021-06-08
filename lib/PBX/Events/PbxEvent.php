<?php
namespace WHMCS\Module\Addon\Simotel\PBX\Events;

use WHMCS\Module\Addon\Simotel\Models\Call;
use WHMCS\Module\Addon\Simotel\PBX\Errors;

abstract class PbxEvent
{
    use Errors;
    protected $request;

    public function __construct()
    {
        $this->request = $_REQUEST;
    }

    abstract public function dispatch(Request $request);
}
