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
    
        if (!isset($input['name']) || !isset($input['avatar'])) {
            return $this->respond(
                ["message" => "Name and avatar are required."], 
                ResponseInterface::HTTP_BAD_REQUEST
            );
        }
    
        $name = $input['name'];
        $avatar = $input['avatar'];
    
        $user = $this->userModel->where('name', $name)->first();
    
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
                return ['name' => $user['name'], 'avatar' => $user['avatar']];
            }, $users);

            $result[$section['name']] = $userNames;
        }

        return $this->respond(
            $result,
            ResponseInterface::HTTP_OK
        );
    }
    

}
