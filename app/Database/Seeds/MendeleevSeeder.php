<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MendeleevSeeder extends Seeder
{
    public function run()
    {
        $mendeleevSectionId = 3; // Section ID for 'Mendeleev'

    $users = [
        // Male
        ['name' => 'ARELLANO, ARIES JAMES, CAUDILLA', 'section_id' => $mendeleevSectionId],
        ['name' => 'COSICO, RUSTINE IAN, PERAJA', 'section_id' => $mendeleevSectionId],
        ['name' => 'GOMEZ, SEAN VIXEN, ACEDILLO', 'section_id' => $mendeleevSectionId],
        ['name' => 'GUEVARRA, LEBRON JOE, ABE', 'section_id' => $mendeleevSectionId],
        ['name' => 'GUTIERREZ, JOSE JUANICO, JAVIER', 'section_id' => $mendeleevSectionId],
        ['name' => 'LABAY, KARL REIGNIER, ALBAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'LAO, CLARENCE JOSH, FALQUERABAO', 'section_id' => $mendeleevSectionId],
        ['name' => 'LUZON, CHRIS ANDREI, ABES', 'section_id' => $mendeleevSectionId],
        ['name' => 'MANRIQUE, KIM NIKOLAI, PANGANIBAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'MASLIAN, EMMANUEL KEN ANGELO, GONZALES', 'section_id' => $mendeleevSectionId],
        ['name' => 'MENDOZA, RAVEN DANREY, GERONIMO', 'section_id' => $mendeleevSectionId],
        ['name' => 'RAMOS, ADAM JOSEPH, MALABANAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'RECTO, JOSH ADRIEL, YLAGAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'TIZON, MC RAINIEL, TURINGAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'VISAYA, THROY ADAM, DILAY', 'section_id' => $mendeleevSectionId],
        ['name' => 'ZAMORA, JAN GABRIEL, -', 'section_id' => $mendeleevSectionId],

        // Female
        ['name' => 'BANTOLINO, ELISHA KIM DEYNIELLE, DUMDUM', 'section_id' => $mendeleevSectionId],
        ['name' => 'BARRIENTOS, ZEA CARYLLE, BOLOR', 'section_id' => $mendeleevSectionId],
        ['name' => 'CONCEPCION, MATHENA FAITH, MAGLUYAN', 'section_id' => $mendeleevSectionId],
        ['name' => 'DE GUZMAN, CARLISLE YSABELLE, MENDOZA', 'section_id' => $mendeleevSectionId],
        ['name' => 'DE GUZMAN, NAIZER ANGELA, BAGSIC', 'section_id' => $mendeleevSectionId],
        ['name' => 'DELA ROCA, XIENDY, BERON', 'section_id' => $mendeleevSectionId],
        ['name' => 'FETILO, ZAREN DAVRAEL, DAYRIT', 'section_id' => $mendeleevSectionId],
        ['name' => 'HERNANDEZ, ZERELLE KEITHLYN MAE, TIAMSIM', 'section_id' => $mendeleevSectionId],
        ['name' => 'HERNANDO, KATE, MANGUIAT', 'section_id' => $mendeleevSectionId],
        ['name' => 'KING, ALIYAH, MANGUIAT', 'section_id' => $mendeleevSectionId],
        ['name' => 'LORETO, PRINCESS AUBREY NYAH, MAGBOO', 'section_id' => $mendeleevSectionId],
        ['name' => 'MALLEON, YANICHEL MYCAH, VILLANUEVA', 'section_id' => $mendeleevSectionId],
        ['name' => 'MANALO, ELIZE KIRSTEN, PEREZ', 'section_id' => $mendeleevSectionId],
        ['name' => 'MEAMO, VANESSA, MAGSINO', 'section_id' => $mendeleevSectionId],
        ['name' => 'MEJICO, RHIEM VIANCH, PASCUAL', 'section_id' => $mendeleevSectionId],
        ['name' => 'MOTA, LOUISE ANNE, MENDOZA', 'section_id' => $mendeleevSectionId],
        ['name' => 'PIADOCHE, SAMANTHA NOREEN, VARGAS', 'section_id' => $mendeleevSectionId],
        ['name' => 'PIGUING, ZOELINE, MATIBAG', 'section_id' => $mendeleevSectionId],
        ['name' => 'QUIAMBAO, JOYCE ERICA, ATIENZA', 'section_id' => $mendeleevSectionId],
        ['name' => 'RAGO, MA LYSSA JERRICA, DEL ROSARIO', 'section_id' => $mendeleevSectionId],
        ['name' => 'SAYAS, EUNICE ALEXIA, -', 'section_id' => $mendeleevSectionId],
        ['name' => 'SUPLEO, KEZIAH GAEL, VIDUYA', 'section_id' => $mendeleevSectionId],
        ['name' => 'TERNIDA, PRINCESS SHARMELLE, FIETAS', 'section_id' => $mendeleevSectionId],
        ['name' => 'TIBAYAN, ALEXANDRA NICOLE, PALMERO', 'section_id' => $mendeleevSectionId],
        ['name' => 'TOLENTINO, MINELLA XANELLE, ESCALONA', 'section_id' => $mendeleevSectionId],
        ['name' => 'VERTUDES, AYEASIA VIELLE, NICOLAS', 'section_id' => $mendeleevSectionId],
        ['name' => 'ZAMORA, CHRISHA MAELER, ASI', 'section_id' => $mendeleevSectionId],
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
