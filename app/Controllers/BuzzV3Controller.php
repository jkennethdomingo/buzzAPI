<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use CodeIgniter\API\ResponseTrait;
use App\Services\PusherService;
use Config\Services;
use Config\Database;

class BuzzV3Controller extends ResourceController
{
    use ResponseTrait;

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
        $this->db = Database::connect();
    }

    public function login()
    {
        $input = $this->request->getJson(true);

        if (empty($input['id']) || empty($input['avatar'])) {
            return $this->respond([
                "data" => null,
                "code" => ResponseInterface::HTTP_BAD_REQUEST,
                "message" => "ID and avatar are required."
            ], ResponseInterface::HTTP_BAD_REQUEST);
        }

        $id = $input['id'];
        $avatar = $input['avatar'];

        $user = $this->db->table('users')
            ->select('users.*, sections.is_active as section_active')
            ->join('sections', 'sections.id = users.section_id', 'left')
            ->where('users.id', $id)
            ->get()
            ->getRowArray();

        if (!$user) {
            return $this->respond([
                "data" => null,
                "code" => ResponseInterface::HTTP_NOT_FOUND,
                "message" => "User not found."
            ], ResponseInterface::HTTP_NOT_FOUND);
        }

        // if ((int)$user['is_online'] === 1) {
        //     return $this->respond([
        //         "data" => null,
        //         "code" => ResponseInterface::HTTP_CONFLICT,
        //         "message" => "User is already logged in."
        //     ], ResponseInterface::HTTP_CONFLICT);
        // }

        if (isset($user['section_active']) && (int)$user['section_active'] !== 1) {
            return $this->respond([
                "data" => null,
                "code" => ResponseInterface::HTTP_FORBIDDEN,
                "message" => "User's section is inactive."
            ], ResponseInterface::HTTP_FORBIDDEN);
        }

        $this->db->table('users')->update(
            [
                'buzzer_sequence' => null,
                'buzzer_pressed_at' => null,
                'is_buzzer_locked' => 0,
                'is_online' => 1,
                'avatar' => $avatar, 
            ],
            ['id' => $id]
        );

        $this->pusher->trigger('login-channel', 'user-logged-in', [
            'user_id' => $user['id'],
            'name' => $user['name'],
            'avatar' => $avatar
        ]);

        return $this->respond([
            "data" => [
                'id' => $user['id'],
                'role' => $user['role']
            ],
            "code" => ResponseInterface::HTTP_OK,
            "message" => "Login successful."
        ], ResponseInterface::HTTP_OK);
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
    
        $adminUsers = $this->userModel->where('role', 'admin')->findAll();
    
        $adminUserNames = array_map(function ($admin) {
            return [
                'id' => $admin['id'],
                'name' => $admin['name'],
                'avatar' => $admin['avatar'],
            ];
        }, $adminUsers);
    
        $result = [];
    
        foreach ($sections as $section) {
            $users = $this->userModel->where('section_id', $section['id'])->findAll();
    
            $sectionUserNames = array_map(function ($user) {
                return [
                    'id' => $user['id'],
                    'name' => $user['name'],
                    'avatar' => $user['avatar'],
                ];
            }, $users);
    
            $allUsers = array_merge($sectionUserNames, $adminUserNames);
    
            $result[] = [
                'section_id' => $section['id'],
                'section_name' => $section['name'],
                'users' => $allUsers,
            ];
        }
    
        return $this->respond(
            $result,
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

        $this->userModel->update($userId, [
            'is_online' => 0,
            'buzzer_sequence' => null,
            'buzzer_pressed_at' => null,
            'is_buzzer_locked' => 0 
        ]);

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

        if ($user['buzzer_sequence'] !== null) {
            return $this->respond(
                ["message" => "Buzzer sequence already recorded."],
                ResponseInterface::HTTP_BAD_REQUEST
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

    public function logoutAllPlayers()
    {
        $this->db->table('users')
            ->where('role', 'player')
            ->update(['is_online' => 0]);

        $this->pusher->trigger('logout-channel', 'all-players-logged-out', [
            'message' => 'All players have been logged out.',
        ]);

        return $this->respond([
            "data" => null,
            "code" => ResponseInterface::HTTP_OK,
            "message" => "All players have been logged out."
        ], ResponseInterface::HTTP_OK);
    }

    public function serveFile()
    {
        $filename = 'tuts.pdf';
        $filePath = WRITEPATH . 'uploads/' . $filename;

        // Check if the file exists
        if (!is_file($filePath)) {
            return $this->response->setStatusCode(404, 'File not found');
        }

        // Serve the file as a response
        return $this->response
            ->setHeader('Content-Type', 'application/pdf') // MIME type for PDF
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setBody(file_get_contents($filePath));
    }

    public function awardScore()
{
    // Parse input
    $input = $this->request->getJSON(true);

    // Validate input
    if (empty($input['user_id']) || !isset($input['score'])) {
        return $this->respond(
            ["message" => "User ID and score are required."],
            ResponseInterface::HTTP_BAD_REQUEST
        );
    }

    $userId = (int) $input['user_id'];
    $score = (int) $input['score'];

    // Validate score
    if ($score < 0) {
        return $this->respond(
            ["message" => "Score must be a non-negative value."],
            ResponseInterface::HTTP_BAD_REQUEST
        );
    }

    // Check if the user exists
    $user = $this->userModel->find($userId);
    if (!$user) {
        return $this->respond(
            ["message" => "User not found."],
            ResponseInterface::HTTP_NOT_FOUND
        );
    }

    // Handle scoring logic
    try {
        $db = \Config\Database::connect();
        $db->transStart();

        // Check if an existing score entry exists for the user
        $existingScore = $this->scoresModel->where([
            'user_id' => $userId,
            'type' => 'recitation', // Assuming we're only dealing with 'recitation' type
            'activity_id' => null // Handle null activity_id based on context
        ])->first();

        if ($existingScore) {
            // Update the score
            $newScore = $existingScore['score'] + $score;
            $this->scoresModel->update($existingScore['id'], [
                'score' => $newScore,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // Insert a new record if it doesn't exist
            $this->scoresModel->insert([
                'user_id' => $userId,
                'type' => 'recitation',
                'activity_id' => null,
                'score' => $score,
                'created_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Commit transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            throw new \RuntimeException("Transaction failed while updating the score.");
        }

        // Calculate cumulative score
        $cumulativeScore = $this->scoresModel
            ->where('user_id', $userId)
            ->selectSum('score')
            ->get()
            ->getRow()
            ->score;

        // Send real-time notification
        $this->pusher->trigger('buzz-channel', 'score-awarded', [
            'user_id' => $userId,
            'new_score' => $cumulativeScore,
            'name' => $user['name']
        ]);

        return $this->respond(
            ["message" => "Score awarded successfully.", "cumulative_score" => $cumulativeScore],
            ResponseInterface::HTTP_OK
        );
    } catch (\Exception $e) {
        log_message('error', "Error awarding score: " . $e->getMessage());
        return $this->respond(
            ["message" => "An error occurred while processing the score."],
            ResponseInterface::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}

    


}
