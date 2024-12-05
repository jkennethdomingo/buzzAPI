<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class V2Table extends Migration
{
    public function up()
    {
        // Create sections table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('name');
        $this->forge->createTable('sections');

        // Create users table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'avatar' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'role' => [
                'type' => 'ENUM',
                'constraint' => ['player', 'admin', 'moderator'],
                'default' => 'player',
            ],
            'is_online' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'buzzer_sequence' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'buzzer_pressed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'is_buzzer_locked' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'section_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('section_id');
        $this->forge->addForeignKey('section_id', 'sections', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('users');

        // Create activities table
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('activities');

        // Create scores table
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
            'type' => [
                'type' => 'ENUM',
                'constraint' => ['recitation', 'activity'],
                'default' => 'recitation',
            ],
            'activity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'null' => true,
            ],
            'score' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('user_id');
        $this->forge->addKey('activity_id');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('activity_id', 'activities', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('scores');

        // Create trigger to add default score on user add
        $triggerSQL = "CREATE TRIGGER add_score_on_user_add AFTER INSERT ON users 
                        FOR EACH ROW BEGIN 
                        INSERT INTO scores (user_id, type, score) VALUES (NEW.id, 'recitation', 0); 
                        END;";
        $this->db->query($triggerSQL);
    }

    public function down()
    {
        // Drop trigger
        $this->db->query('DROP TRIGGER IF EXISTS add_score_on_user_add');

        // Drop tables
        $this->forge->dropTable('scores', true);
        $this->forge->dropTable('activities', true);
        $this->forge->dropTable('sections', true);
        $this->forge->dropTable('users', true);
    }
}
