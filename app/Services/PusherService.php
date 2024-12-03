<?php

namespace App\Services;

use Config\Pusher as PusherConfig;
use Pusher\Pusher;

class PusherService
{
    private $pusher;

    public function __construct()
    {
        $config = new PusherConfig();

        $this->pusher = new Pusher(
            $config->key,
            $config->secret,
            $config->appId,
            [
                'cluster' => $config->cluster,
                'useTLS'  => $config->encrypted
            ]
        );
    }

    public function trigger(string $channel, string $event, array $data)
    {
        return $this->pusher->trigger($channel, $event, $data);
    }
}
