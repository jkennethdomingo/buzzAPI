<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use App\Services\PusherService;
use Config\Services;

class BuzzV2Controller extends ResourceController
{
    protected $buzzerStateModel;
    protected $scoresModel;
    protected $userModel;
    protected $sectionsModel;
    protected $pusher;
    protected $db;

    public function __construct()
    {
        $this->scoresModel = Services::scoresModel();
        $this->userModel = Services::userModel();
        $this->sectionsModel = Services::sectionsModel();
        $this->pusher = new PusherService();
        $this->db = \Config\Database::connect();
    }

    public function login()
    {
        $input = $this->request->getJson(true);

        if (!isset($input['id']) || !isset($input['avatar'])) {
            return $this->respond(
                ["message" => "ID and avatar are required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $id = $input['id'];
        $avatar = $input['avatar'];

        $user = $this->userModel->find($id);

        if (!$user) {
            return $this->respond(
                ["message" => "User not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        $section = $this->sectionsModel->find($user['section_id']);
        if (!$section || !$section['is_active']) {
            return $this->respond(
                ["message" => "User's section is inactive."],
                ResponseInterface::HTTP_FORBIDDEN
            );
        }

        $this->db->table('users')->where('id', $user['id'])->update([
            'buzzer_sequence' => null,
            'buzzer_pressed_at' => null,
            'is_buzzer_locked' => 0
        ]);

        $this->userModel->update($user['id'], [
            'is_online' => 1,
            'avatar' => $avatar
        ]);

        $this->pusher->trigger('login-channel', 'user-logged-in', [
            'user_id' => $user['id'],
            'name' => $user['name'],
            'avatar' => $avatar
        ]);

        return $this->respond(
            [
                'id' => $user['id'],
                'role' => $user['role']
            ],
            ResponseInterface::HTTP_OK
        );
    }


    public function getSectionGrouping()
    {
        $sections = $this->sectionsModel->findAll();

        if (!$sections) {
            return $this->respond(
                ["message" => "No sections found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        $result = [];

        foreach ($sections as $section) {
            $users = $this->userModel->where('section_id', $section['id'])->findAll();

            $userNames = array_map(function ($user) {
                return [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'avatar' => $user['avatar']
                ];
            }, $users);

            $result[] = [
                'section_id' => $section['id'],
                'section_name' => $section['name'],
                'users' => $userNames
            ];
        }

        return $this->respond(
            $result,
            ResponseInterface::HTTP_OK
        );
    }

    public function buzz()
    {
        $input = $this->request->getJson(true);

        if (!isset($input['user_id'])) {
            return $this->respond(
                ["message" => "User ID is required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $userId = $input['user_id'];

        $user = $this->userModel->find($userId);

        if (!$user) {
            return $this->respond(
                ["message" => "User not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        $maxSequence = $this->db->table('users')
            ->selectMax('buzzer_sequence', 'max_sequence')
            ->get()
            ->getRowArray()['max_sequence'] ?? 0;

        $this->userModel->update($userId, [
            'is_buzzer_locked' => 1,
            'buzzer_pressed_at' => date('Y-m-d H:i:s'),
            'buzzer_sequence' => $maxSequence + 1
        ]);

        // Fetch the updated user state
        $updatedUser = $this->userModel->find($userId);

        $this->pusher->trigger('buzz-channel', 'buzz-event', [
            'user_id' => $userId,
            'sequence' => $updatedUser['buzzer_sequence'],
            'name' => $updatedUser['name'],
        ]);

        return $this->respond(
            [
                "message" => "Buzz recorded successfully.",
                "is_buzzer_locked" => $updatedUser['is_buzzer_locked']
            ],
            ResponseInterface::HTTP_OK
        );
    }


    public function getStudentsBySection($sectionId)
    {
        if (!$sectionId) {
            return $this->respond(
                ["message" => "Section ID is required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $section = $this->sectionsModel->find($sectionId);
        if (!$section) {
            return $this->respond(
                ["message" => "Section not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        $students = $this->userModel
            ->select('id, name, avatar, buzzer_sequence AS sequence, is_online, is_buzzer_locked')
            ->where('section_id', $sectionId)
            ->findAll();

        if (empty($students)) {
            return $this->respond(
                ["message" => "No students found in this section."],
                ResponseInterface::HTTP_OK
            );
        }

        return $this->respond(
            $students,
            ResponseInterface::HTTP_OK
        );
    }

    public function awardScore()
    {
        $input = $this->request->getJSON(true);

        if (!isset($input['user_id'], $input['score'])) {
            return $this->respond(
                ["message" => "User ID and score are required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $userId = $input['user_id'];
        $score = (int) $input['score'];

        // Check if the user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->respond(
                ["message" => "User not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        // Check if a score record for the user already exists
        $existingScore = $this->scoresModel->where('user_id', $userId)->first();

        if ($existingScore) {
            // Update the existing score
            $newScore = $existingScore['score'] + $score; // Add the new score to the existing score
            $this->scoresModel->update($existingScore['id'], [
                'score' => $newScore,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert the new score if no record exists
            $this->scoresModel->insert([
                'user_id' => $userId,
                'score' => $score,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Calculate the cumulative score for the user
        $cumulativeScore = $this->scoresModel
            ->where('user_id', $userId)
            ->selectSum('score')
            ->get()
            ->getRow()
            ->score;

        // Notify via Pusher
        $this->pusher->trigger('buzz-channel', 'score-awarded', [
            'user_id' => $userId,
            'new_score' => $cumulativeScore,
            'name' => $user['name']
        ]);

        return $this->respond(
            ["message" => "Score awarded successfully."],
            ResponseInterface::HTTP_OK
        );
    }




    public function getSectionNameById($id)
    {
        if (!$id) {
            return $this->respond(
                ["message" => "Section ID is required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $section = $this->sectionsModel->find($id);

        if (!$section) {
            return $this->respond(
                ["message" => "Section not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        return $this->respond(
            ["section_name" => $section['name']],
            ResponseInterface::HTTP_OK
        );
    }

    public function resetBuzzerState()
    {

        $this->db->table('users')
            ->update([
                'is_buzzer_locked' => 0,
                'buzzer_sequence' => null,
                'buzzer_pressed_at' => null
            ]);


            $this->pusher->trigger('buzz-channel', 'score-awarded', [
            ]);

        return $this->respond(
            ["message" => "Buzzer state reset for all users."],
            ResponseInterface::HTTP_OK
        );
    }
    
    public function logout()
    {
        $input = $this->request->getJson(true);

        // Check if the user_id is provided
        if (!isset($input['user_id'])) {
            return $this->respond(
                ["message" => "User ID is required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $userId = $input['user_id'];

        // Check if the user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->respond(
                ["message" => "User not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        // Update the user's status to offline and clear buzzer state
        $this->userModel->update($userId, [
            'is_online' => 0,  // Set user status to offline
            'buzzer_sequence' => null,  // Clear the buzzer sequence
            'buzzer_pressed_at' => null,  // Clear the timestamp for buzzer press
            'is_buzzer_locked' => 0  // Unlock the buzzer
        ]);

        // Trigger Pusher event for user logout
        $this->pusher->trigger('buzz-channel', 'user-logged-out', [
            'user_id' => $userId,
            'name' => $user['name'],
            'message' => 'User logged out successfully.'
        ]);

        return $this->respond(
            ["message" => "User logged out successfully."],
            ResponseInterface::HTTP_OK
        );
    }
    

    public function getBuzzerState()
    {
        $input = $this->request->getJson(true);
    
        // Check if the user_id is provided
        if (!isset($input['user_id'])) {
            return $this->respond(
                ["message" => "User ID is required."],
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    
        $userId = $input['user_id'];
    
        // Check if the user exists
        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->respond(
                ["message" => "User not found."],
                ResponseInterface::HTTP_NOT_FOUND
            );
        }
    
        // Retrieve the buzzer state for the user
        $buzzerState = [
            'buzzer_sequence' => $user['buzzer_sequence'],
            'buzzer_pressed_at' => $user['buzzer_pressed_at'],
            'is_buzzer_locked' => $user['is_buzzer_locked']
        ];
    
        return $this->respond(
            $buzzerState,
            ResponseInterface::HTTP_OK
        );
    }
    

    public function getScores()
    {

    }

}
