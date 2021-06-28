<?php

namespace Hsy\Simotel\SimotelApi;

use WHMCS\Module\Addon\Simotel\WhmcsOperations;

class Response
{
    public $success;
    public $message;
    public $data;
    public $contents;
    public $res;

    public function __construct($response)
    {

        $this->res = clone $response;
        $contents = $response->getBody()->getContents();
        $this->contents = $contents;
//        $responseBodyContent = json_decode($contents);
//        $this->success = $responseBodyContent->success;
//        $this->message = $responseBodyContent->message;
//        $this->data = $responseBodyContent->data;

    }
}
