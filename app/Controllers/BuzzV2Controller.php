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

        $this->pusher->trigger('buzz-channel', 'buzz-event', [
            'user_id' => $userId,
            'sequence' => $maxSequence + 1,
            'name' => $user['name'],
        ]);

        return $this->respond(
            ["message" => "Buzz recorded successfully."], 
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
        $input = $this->request->getJson(true);

        if (!isset($input['user_id'], $input['score'])) {
            return $this->respond(
                ["message" => "User ID and score are required."], 
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }

        $userId = $input['user_id'];
        $score = (int)$input['score'];

        $user = $this->userModel->find($userId);
        if (!$user) {
            return $this->respond(
                ["message" => "User not found."], 
                ResponseInterface::HTTP_NOT_FOUND
            );
        }

        $this->userModel->update($userId, [
            'score' => $user['score'] + $score
        ]);

        $this->userModel->update(null, [
            'is_buzzer_locked' => 0,
            'buzzer_sequence' => null,
            'buzzer_pressed_at' => null
        ]);

        $this->pusher->trigger('buzz-channel', 'score-awarded', [
            'user_id' => $userId,
            'new_score' => $user['score'] + $score,
            'name' => $user['name']
        ]);

        return $this->respond(
            ["message" => "Score awarded and buzzer state reset."], 
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

    

}
