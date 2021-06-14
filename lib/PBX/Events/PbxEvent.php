<?php
namespace WHMCS\Module\Addon\Simotel\PBX\Events;

use WHMCS\Module\Addon\Simotel\PBX\Errors;
use WHMCS\Module\Addon\Simotel\Request;

abstract class PbxEvent
{
    use Errors;
    protected $request;

    public function __construct()
    {
        $this->request = new Request();
    }
    abstract public function dispatch();
}
