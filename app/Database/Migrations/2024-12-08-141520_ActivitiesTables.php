<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ActivitiesTables extends Migration
{
    public function up()
    {
        // Modify activities table to add score column
        $this->forge->addColumn('activities', [
            'score' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
                'after' => 'name', // Place the column after 'name'
            ],
        ]);

        // Create user_activities table
        $this->forge->addField([
            'user_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'activity_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
            ],
            'score' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'is_done' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
            ],
            'sequence' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
        ]);
        $this->forge->addKey(['user_id', 'activity_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('activity_id', 'activities', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('user_activities');
    }

    public function down()
    {
        // Drop user_activities table
        $this->forge->dropTable('user_activities', true);

        // Remove score column from activities table
        $this->forge->dropColumn('activities', 'score');
    }
}
