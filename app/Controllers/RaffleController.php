<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Models\RaffleModel;
use CodeIgniter\HTTP\ResponseInterface;

class RaffleController extends ResourceController
{
    use ResponseTrait;

    public function getParticipants()
    {
        $raffleModel = new RaffleModel();

        try {
            // Fetch all participants
            $participants = $raffleModel->findAll();

            // Return success response
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'message' => 'Participants retrieved successfully.',
                'data' => $participants
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return $this->respond([
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while retrieving participants.',
                'error' => $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
