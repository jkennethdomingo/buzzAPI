<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SectionSeeder extends Seeder
{
    public function run()
    {
        $data = [
            ['name' => 'Hawking', 'is_active' => 0],
            ['name' => 'Faraday', 'is_active' => 0],
            ['name' => 'Mendeleev', 'is_active' => 0],
            ['name' => 'Pascal', 'is_active' => 0],
        ];

        // Insert the data into the sections table
        $this->db->table('sections')->insertBatch($data);
    }
}
