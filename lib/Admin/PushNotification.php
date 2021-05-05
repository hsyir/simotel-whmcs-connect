<?php

namespace WHMCS\Module\Addon\Simotel\Admin;

use GuzzleHttp\Client;
use Pusher\Pusher;

class PushNotification
{
    /**
     * @var $pusher \Pusher\Pusher
     */
    private $pusher;

    public function __construct()
    {

        $moduleConfig = WhmcsOperations::getConfig();

        $config["app_key"] = $moduleConfig["WsAppKey"];
        $config["app_id"] = $moduleConfig["WsAppId"];
        $config["host"] = $moduleConfig["WsHost"];
        $config["port"] = $moduleConfig["WsPort"];
        $config["cluster"] = $moduleConfig["WsCluster"];
        $config["secret"] = $moduleConfig["WsSecret"];

        // disable curl certificate verification
        $guzzleClient = new Client([
            'verify' => false
        ]);

        $this->pusher = new Pusher(
            $config["app_key"],
            $config["secret"],
            $config["app_id"],
            array(
                'cluster' => $config["cluster"],
                "host" => "pusher.hsy.ir",
                "port" =>$config["port"],
                "scheme"=>"https"
            ),
            $guzzleClient
        );

    }

    public function send($channel, $event, $data)
    {
        $this->pusher->trigger($channel, $event, $data);
    }


    public function authorize($channel)
    {
        return $this->pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
    }


}
