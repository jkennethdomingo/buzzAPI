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

    public function addParticipant()
    {
        $raffleModel = new RaffleModel();

        // Validate input
        $validation = \Config\Services::validation();

        $validation->setRules([
            'firstname' => 'required|min_length[3]|max_length[50]',
            'lastname'  => 'required|min_length[3]|max_length[50]'
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->respond([
                'status' => ResponseInterface::HTTP_BAD_REQUEST,
                'message' => 'Validation failed.',
                'errors' => $validation->getErrors()
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        try {
            // Prepare the data
            $data = [
                'firstname' => $this->request->getVar('firstname'),
                'lastname'  => $this->request->getVar('lastname'),
                'has_won'   => '0' // Default to '0' meaning not yet won
            ];

            // Insert the participant into the database
            $raffleModel->insert($data);

            // Return success response
            return $this->respond([
                'status' => ResponseInterface::HTTP_CREATED,
                'message' => 'Participant added successfully.',
                'data' => $data
            ], ResponseInterface::HTTP_CREATED);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return $this->respond([
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while adding the participant.',
                'error' => $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
