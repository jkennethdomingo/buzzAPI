<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Services\PusherService;
use Config\Services;

class BuzzController extends ResourceController
{
    protected $buzzerStateModel;
    protected $logsModel;
    protected $scoresModel;
    protected $userModel;
    protected $pusher;

    public function __construct()
    {
        $this->buzzerStateModel = Services::buzzerStateModel();
        $this->logsModel = Services::logsModel();
        $this->scoresModel = Services::scoresModel();
        $this->userModel = Services::userModel();
        $this->pusher = new PusherService();
    }

    public function createUser()
    {
        $data = $this->request->getJSON(true);

        // Validate required fields
        if (!isset($data['name']) || !isset($data['section']) || !isset($data['avatar'])) {
            return $this->respond(['error' => 'Missing required fields'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        // Check if user with the same name exists
        $existingUser = $this->userModel->where('name', $data['name'])->first();

        if ($existingUser) {
            // User exists, return success with existing user details
            return $this->respond(['message' => 'User exists', 'id' => $existingUser['id']], ResponseInterface::HTTP_OK);
        }

        // Prepare new user data
        $userData = [
            'name' => $data['name'],
            'section' => $data['section'],
            'avatar' => $data['avatar'],
            'role' => 'player',
        ];

        // Insert new user
        $userId = $this->userModel->insert($userData);

        if ($userId) {
            return $this->respond(['message' => 'User created', 'id' => $userId], ResponseInterface::HTTP_CREATED);
        }

        // If insertion fails, return an error
        return $this->respond(['error' => 'Failed to create user'], ResponseInterface::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function getBuzzerState()
    {
        $buzzerState = $this->buzzerStateModel->find(1); // Assuming only one buzzer state record

        if ($buzzerState) {
            $response = [
                'is_locked' => $buzzerState['is_locked'],
                'user' => null,
            ];

            if ($buzzerState['user_id']) {
                $user = $this->userModel->find($buzzerState['user_id']);
                $response['user'] = [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'avatar' => $user['avatar'],
                ];
            }

            return $this->respond($response, ResponseInterface::HTTP_OK);
        }

        return $this->respond(['error' => 'Buzzer state not found'], ResponseInterface::HTTP_NOT_FOUND);
    }

    public function pressBuzzer()
    {
        $data = $this->request->getJSON(true);
        $userId = $data['user_id'] ?? null;

        if (!$userId || !$this->userModel->find($userId)) {
            return $this->respond(['error' => 'Invalid user ID'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $buzzerState = $this->buzzerStateModel->find(1);

        if ($buzzerState['is_locked']) {
            return $this->respond(['error' => 'Buzzer is locked'], ResponseInterface::HTTP_CONFLICT);
        }

        $this->buzzerStateModel->update(1, [
            'user_id' => $userId,
            'pressed_at' => date('Y-m-d H:i:s'),
            'is_locked' => true,
        ]);

        $user = $this->userModel->find($userId);

        $this->pusher->trigger('buzzer-channel', 'buzzer-pressed', [
            'user_id' => $user['id'],
            'name' => $user['name'],
            'avatar' => $user['avatar'],
            'pressed_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->respond(['message' => 'Buzzer pressed'], ResponseInterface::HTTP_OK);
    }


    public function awardScore()
    {
        $data = $this->request->getJSON(true);

        $userId = $data['user_id'] ?? null;
        $score = $data['score'] ?? null;
        $award = $data['award'] ?? false;

        if (!$userId || !$this->userModel->find($userId) || !is_numeric($score)) {
            return $this->respond(['error' => 'Invalid input data'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        if ($award) {
            $this->scoresModel->insert([
                'user_id' => $userId,
                'score' => $score,
            ]);

        }

        // Reset the buzzer state
        $this->buzzerStateModel->update(1, [
            'user_id' => null,
            'pressed_at' => null,
            'is_locked' => false,
        ]);

        // Trigger Pusher event for buzzer reset
        $this->pusher->trigger('buzzer-channel', 'buzzer-reset', []);

        return $this->respond(['message' => 'Score awarded and buzzer reset'], ResponseInterface::HTTP_OK);
    }

    public function getAllStudents($section)
    {
        if (empty($section)) {
            return $this->respond(['error' => 'Section is required'], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $students = $this->userModel
            ->where('section', $section)
            ->where('role', 'player') 
            ->select('name, avatar, role')
            ->findAll();

        if (!$students) {
            return $this->respond(['error' => 'No students found for the given section'], ResponseInterface::HTTP_NOT_FOUND);
        }

        return $this->respond(['students' => $students], ResponseInterface::HTTP_OK);
    }






}
