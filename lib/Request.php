<?php
namespace WHMCS\Module\Addon\Simotel;

class Request
{
    public function __get($name)
    {
        return $_REQUEST[$name];
    }
}
