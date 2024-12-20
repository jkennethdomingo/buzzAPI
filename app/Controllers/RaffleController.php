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


    public function markAsWinner($id)
    {
        $raffleModel = new RaffleModel();

        try {
            // Check if participant exists
            $participant = $raffleModel->find($id);
            if (!$participant) {
                return $this->respond([
                    'status' => ResponseInterface::HTTP_NOT_FOUND,
                    'message' => 'Participant not found.'
                ], ResponseInterface::HTTP_NOT_FOUND);
            }

            // Update the has_won field to 1
            $raffleModel->update($id, ['has_won' => 1]);

            // Return success response
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'message' => 'Participant marked as winner successfully.',
                'data' => $participant
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return $this->respond([
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while marking the participant as a winner.',
                'error' => $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function editParticipant($id)
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
                'lastname'  => $this->request->getVar('lastname')
            ];

            // Update the participant in the database
            $raffleModel->update($id, $data);

            // Return success response
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'message' => 'Participant updated successfully.',
                'data' => $data
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return $this->respond([
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while updating the participant.',
                'error' => $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function deleteParticipant($id)
    {
        $raffleModel = new RaffleModel();

        try {
            // Check if participant exists
            $participant = $raffleModel->find($id);
            if (!$participant) {
                return $this->respond([
                    'status' => ResponseInterface::HTTP_NOT_FOUND,
                    'message' => 'Participant not found.'
                ], ResponseInterface::HTTP_NOT_FOUND);
            }

            // Delete the participant from the database
            $raffleModel->delete($id);

            // Return success response
            return $this->respond([
                'status' => ResponseInterface::HTTP_OK,
                'message' => 'Participant deleted successfully.'
            ], ResponseInterface::HTTP_OK);
        } catch (\Exception $e) {
            // Return error response in case of failure
            return $this->respond([
                'status' => ResponseInterface::HTTP_INTERNAL_SERVER_ERROR,
                'message' => 'An error occurred while deleting the participant.',
                'error' => $e->getMessage()
            ], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
