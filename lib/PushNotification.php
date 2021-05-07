<?php

namespace WHMCS\Module\Addon\Simotel;

use GuzzleHttp\Client;
use Pusher\Pusher;

/**
 *  Push notification pusher base connector
 */
class PushNotification
{
    /**
     * pusher instance
     * @var $pusher \Pusher\Pusher
     */
    private $pusher;

    /**
     * PushNotification constructor.
     * @throws \Pusher\PusherException
     */
    public function __construct()
    {
        $this->pusher = $this->createPusherInstance();
    }

    /**
     * send message to channel
     *
     * @param $channel
     * @param $event
     * @param $data
     * @throws \Pusher\ApiErrorException
     * @throws \Pusher\PusherException
     */
    public function send($channel, $event, $data)
    {
        $this->pusher->trigger($channel, $event, $data);
    }


    public function authorize($channel)
    {
        return $this->pusher->socket_auth($_POST['channel_name'], $_POST['socket_id']);
    }

    /**
     * create pusher instance
     *
     * @return Pusher
     * @throws \Pusher\PusherException
     */
    private function createPusherInstance(): Pusher
    {
        $moduleConfig = WhmcsOperations::getConfig();

        // disable curl certificate verification
        $guzzleClient = new Client([
            'verify' => false
        ]);

        return new Pusher(
            $moduleConfig["WsAppKey"],
            $moduleConfig["WsSecret"],
            $moduleConfig["WsAppId"],
            array(
                'cluster' => $moduleConfig["WsCluster"],
                "host" => $moduleConfig["WsHost"],
                "port" => $moduleConfig["WsPort"],
                "scheme" => "https"
            ),
            $guzzleClient
        );
    }


}
