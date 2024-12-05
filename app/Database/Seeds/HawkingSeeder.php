<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class HawkingSeeder extends Seeder
{
    public function run()
    {
        $hawkingSectionId = 1; // Replace this with the actual ID for the 'Hawking' section

        $users = [
            // Male
            ['name' => 'CLEOFE, ARWYN MIGUEL R.', 'section_id' => $hawkingSectionId],
            ['name' => 'COZ, SANTINO D.', 'section_id' => $hawkingSectionId],
            ['name' => 'DELA CRUZ, PATRICK LEI MON D.', 'section_id' => $hawkingSectionId],
            ['name' => 'DE LEON, XYRUZ DENVER T.', 'section_id' => $hawkingSectionId],
            ['name' => 'DUQUIL, LIAM JERICO E.', 'section_id' => $hawkingSectionId],
            ['name' => 'GASTALLA, PAUL JAKE M.', 'section_id' => $hawkingSectionId],
            ['name' => 'MADRIAGA, GABRIEL LOUIE G.', 'section_id' => $hawkingSectionId],
            ['name' => 'MARQUEZ, MIGUEL ANGELO C.', 'section_id' => $hawkingSectionId],
            ['name' => 'MENDOZA, KWON DOMENIC D.', 'section_id' => $hawkingSectionId],
            
            // Female
            ['name' => 'ALEGRE, ALLESSANDRA MARIS L.', 'section_id' => $hawkingSectionId],
            ['name' => 'AREVALO, ROJAN MARISSE A.', 'section_id' => $hawkingSectionId],
            ['name' => 'ASPECTO, CHARLENE JHEN D.', 'section_id' => $hawkingSectionId],
            ['name' => 'BARAL, MICKAELA RHEYANAH DENISSE M.', 'section_id' => $hawkingSectionId],
            ['name' => 'CARINGAL, RHIANNA EZRA R.', 'section_id' => $hawkingSectionId],
            ['name' => 'CHIYUTO, JANA SHECAINA C.', 'section_id' => $hawkingSectionId],
            ['name' => 'FABUNAN, ARIANE ZEAH', 'section_id' => $hawkingSectionId],
            ['name' => 'GERON, AVEENA DANIELLA R.', 'section_id' => $hawkingSectionId],
            ['name' => 'LATORZA, MARY GALADRIEL H.', 'section_id' => $hawkingSectionId],
            ['name' => 'LAUDENCIA, LIANNA CALYN P.', 'section_id' => $hawkingSectionId],
            ['name' => 'MARAMOT, ALEXIS S.', 'section_id' => $hawkingSectionId],
            ['name' => 'MASTORILLAS, SAPPHIRE', 'section_id' => $hawkingSectionId],
            ['name' => 'MENDOZA, KIARA ELAINE C.', 'section_id' => $hawkingSectionId],
            ['name' => 'QUIJALVO, SOPHIA DENISE C.', 'section_id' => $hawkingSectionId],
            ['name' => 'RAMOS, REISHA JEHN L.', 'section_id' => $hawkingSectionId],
            ['name' => 'RIVA, ZENAIDA G.', 'section_id' => $hawkingSectionId],
            ['name' => 'ROLDAN, NIŇA KRIANE Z.', 'section_id' => $hawkingSectionId],
            ['name' => 'TALAMOR, JHOELLE C.', 'section_id' => $hawkingSectionId],
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
