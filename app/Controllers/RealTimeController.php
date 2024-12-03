<?php

namespace App\Controllers;

use App\Services\PusherService;
use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class RealTimeController extends BaseController
{
    private $pusherService;

    public function __construct()
    {
        $this->pusherService = new PusherService();
    }

    public function notify()
    {
        $data = $this->request->getJSON(true);

        if (!$data) {
            return $this->response->setJSON(['error' => 'Invalid data'])->setStatusCode(ResponseInterface::HTTP_BAD_REQUEST);
        }

        $channel = $data['channel'] ?? 'default-channel';
        $event = $data['event'] ?? 'default-event';
        $payload = $data['payload'] ?? [];

        $this->pusherService->trigger($channel, $event, $payload);

        return $this->response->setJSON(['message' => 'Event triggered successfully']);
    }
}
