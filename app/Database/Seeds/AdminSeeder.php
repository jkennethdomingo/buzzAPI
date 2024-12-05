<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            ['name' => 'Domingo, John Kenneth Nicko M.'],
            ['name' => 'Malaluan, Jan Dyze'],
            ['name' => 'Golfo, Ellyssa S.'],
            ['name' => 'Abaja, Milko GILORDE A'],
            ['name' => 'Mercado, Patrick Kristoffer C.'],
        ];

        foreach ($admins as &$admin) {
            $admin = array_merge($admin, [
                'avatar' => null,
                'role' => 'admin',
                'is_online' => 0,
                'buzzer_sequence' => null,
                'buzzer_pressed_at' => null,
                'is_buzzer_locked' => 0,
                'section_id' => null,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Insert the data into the users table
        $this->db->table('users')->insertBatch($admins);
    }
}
