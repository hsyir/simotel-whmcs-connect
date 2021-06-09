<?php
namespace WHMCS\Module\Addon\Simotel\PBX\Events;

class Request
{
    public function __get($name)
    {
        return $_REQUEST[$name];
    }
}
