<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class PascalSeeder extends Seeder
{
    public function run()
    {
        $pascalSectionId = 4; // Section ID for 'Pascal'

    $users = [
        // Male
        ['name' => 'BALDOVINO, AMBER LEIF, FELIZAR', 'section_id' => $pascalSectionId],
        ['name' => 'ESPINOSA, YCE AITHAN, LARANANG', 'section_id' => $pascalSectionId],
        ['name' => 'GARCIA, EUGENE, MAGAT', 'section_id' => $pascalSectionId],
        ['name' => 'GEROLEO, ROAN LUTHER, DE CASTRO', 'section_id' => $pascalSectionId],
        ['name' => 'MACATANGAY, MARK ADRIAN, PACHECO', 'section_id' => $pascalSectionId],
        ['name' => 'MARASIGAN, JOHN DANIEL, DELOS REYES', 'section_id' => $pascalSectionId],
        ['name' => 'VILLAS, JONAS ELIJAH, ZAMORA', 'section_id' => $pascalSectionId],

        // Female
        ['name' => 'BAGUIO, FAITH ELAISSA MARIZ, SALAZAR', 'section_id' => $pascalSectionId],
        ['name' => 'BALDONO, LEILANJIE NOVA, GRANIL', 'section_id' => $pascalSectionId],
        ['name' => 'BARAQUEL, LIAN MARGARETTE, BASAS', 'section_id' => $pascalSectionId],
        ['name' => 'BAUTISTA, ROHAN KYLE, CLATON', 'section_id' => $pascalSectionId],
        ['name' => 'BORSOTO, ESTER, DRIS', 'section_id' => $pascalSectionId],
        ['name' => 'CASTRO, IRISH FIELLE, LABAY', 'section_id' => $pascalSectionId],
        ['name' => 'CRUZ, FRANCHESCA, MATCHIMURA', 'section_id' => $pascalSectionId],
        ['name' => 'CUENCA, LYZADIEN, ZABATE', 'section_id' => $pascalSectionId],
        ['name' => 'DEL PUERTO, NICOLE, ARELLANO', 'section_id' => $pascalSectionId],
        ['name' => 'DEQUIROS, SHAIRA ANGEL, BAWI-IN', 'section_id' => $pascalSectionId],
        ['name' => 'DIAZ, GABBY ROSE, DAYRIT', 'section_id' => $pascalSectionId],
        ['name' => 'FALOGME, JOHANNA MISHA, LLAVE', 'section_id' => $pascalSectionId],
        ['name' => 'GARCIA, DANNEA, PINEDA', 'section_id' => $pascalSectionId],
        ['name' => 'GARONG, JANAH MARCELA, ADARLO', 'section_id' => $pascalSectionId],
        ['name' => 'JIMENEZ, TATIANNA NATHALIE, FIESTADA', 'section_id' => $pascalSectionId],
        ['name' => 'LICUAN, NICKA, AGUILAR', 'section_id' => $pascalSectionId],
        ['name' => 'LIPATA, XYRIEL, -', 'section_id' => $pascalSectionId],
        ['name' => 'LOPEZ, ASHLEY DENISSE, ATIENZA', 'section_id' => $pascalSectionId],
        ['name' => 'MAGDATO, CRISALYN, DALDE', 'section_id' => $pascalSectionId],
        ['name' => 'MAULION, JULIANA REINMARIE, ESTRADA', 'section_id' => $pascalSectionId],
        ['name' => 'PALACIOS, JEIDEN, RAMOS', 'section_id' => $pascalSectionId],
        ['name' => 'REGINIO, ALEXSSANDRAE JAIRA, ALDOVINO', 'section_id' => $pascalSectionId],
        ['name' => 'SADIWA, ANGEL, ZULUETA', 'section_id' => $pascalSectionId],
    ];
    
    foreach ($users as &$user) {
        $user = array_merge($user, [
            'avatar' => null,
            'role' => 'player',
            'is_online' => 0,
            'buzzer_sequence' => null,
            'buzzer_pressed_at' => null,
            'is_buzzer_locked' => 0,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Insert the data into the users table
    $this->db->table('users')->insertBatch($users);
    }
}
