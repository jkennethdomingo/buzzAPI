<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AttendanceTable extends Migration
{
    public function up()
    {
        // Create attendance table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'device' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'pc_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true, // Make this field nullable
            ],
            'date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'is_present' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('attendance');
    }

    public function down()
    {
        // Drop attendance table
        $this->forge->dropTable('attendance', true);
    }
}
