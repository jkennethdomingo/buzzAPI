<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class FaradaySeeder extends Seeder
{
    public function run()
    {
        $hawkingSectionId = 2; // Replace this with the actual ID for the 'Hawking' section

        $users = [
            // Male
            ['name' => 'ADEVA, JERIE AZRIEL, MARINDUQUE', 'section_id' => $hawkingSectionId],
            ['name' => 'ALVAREZ, VON JARED, SARTURIO', 'section_id' => $hawkingSectionId],
            ['name' => 'BANUELOS, KENSHIN RAELEY, ACEDILLO', 'section_id' => $hawkingSectionId],
            ['name' => 'BARCELONA, JOMI ORLEANZE, LOTO', 'section_id' => $hawkingSectionId],
            ['name' => 'CEPILLO, KEIAN RHEIYMOND, MADRIGAL', 'section_id' => $hawkingSectionId],
            ['name' => 'CHAVEZ, SHIA GABRYL, TRANCO', 'section_id' => $hawkingSectionId],
            ['name' => 'CUASAY, KEVIN EMMANUEL, REPIL', 'section_id' => $hawkingSectionId],
            ['name' => 'GARCIA, MCLARIUS EMMIL CHAMZEL, DOLOR', 'section_id' => $hawkingSectionId],
            ['name' => 'JUMIG, DWANE EMERSON, ALBERTO', 'section_id' => $hawkingSectionId],
            ['name' => 'MALABANAN, LANCE MARTIN, SZE', 'section_id' => $hawkingSectionId],
            ['name' => 'MENDOZA, AIRIK, LEVISTE', 'section_id' => $hawkingSectionId],
            ['name' => 'NAVARRO, ALEXCEAN CHROE, LAYLAY', 'section_id' => $hawkingSectionId],
            ['name' => 'QUIAMBAO, JED EZEKIEL, MANGARIN', 'section_id' => $hawkingSectionId],
            ['name' => 'RETA, RAEL MATTHEW, ARELLANO', 'section_id' => $hawkingSectionId],
            ['name' => 'TAGALOG, ZEDDRICK JOSEPH, ACOSTA', 'section_id' => $hawkingSectionId],
            ['name' => 'ZABATE, ZAIJAN DAINIEL, CUENCA', 'section_id' => $hawkingSectionId],

            // Female
            ['name' => 'ANDES, PRECIOUS ANN, REGALA', 'section_id' => $hawkingSectionId],
            ['name' => 'ASI, ADANAH JOSSET, MENDROS', 'section_id' => $hawkingSectionId],
            ['name' => 'BALINA, ALEISHA JAEDAH, LUNARIO', 'section_id' => $hawkingSectionId],
            ['name' => 'CARINGAL, SOFIA MARIZ, INGCO', 'section_id' => $hawkingSectionId],
            ['name' => 'CASTILLO, CHARLIE KYLEE, MANALO', 'section_id' => $hawkingSectionId],
            ['name' => 'CLEOFE, MARY AUBREY GABREL, JALOS', 'section_id' => $hawkingSectionId],
            ['name' => 'CORTES, JAN CHELZYA, MONTEALEGRE', 'section_id' => $hawkingSectionId],
            ['name' => 'DELA CRUZ, RIAMELLE, HERNANDEZ', 'section_id' => $hawkingSectionId],
            ['name' => 'DELAS ALAS, CANDIZE IYAH, GUSTILO', 'section_id' => $hawkingSectionId],
            ['name' => 'DESUYO, DENISE YUANNE, TANYAG', 'section_id' => $hawkingSectionId],
            ['name' => 'FERNANDEZ, YZABELLA ANDRE, YONAHA', 'section_id' => $hawkingSectionId],
            ['name' => 'GUAVIS, PAULA JANE, BALANON', 'section_id' => $hawkingSectionId],
            ['name' => 'GUTIEREZ, RAVEN ROSE, DAYANGHIRANG', 'section_id' => $hawkingSectionId],
            ['name' => 'LAGUERTA, ROELA ZARIAH, FLORES', 'section_id' => $hawkingSectionId],
            ['name' => 'LAZARO, PRINCESS AESHEL, ITA-AS', 'section_id' => $hawkingSectionId],
            ['name' => 'LOPEZ, HAE WON FAITH, VITAN', 'section_id' => $hawkingSectionId],
            ['name' => 'MAGCAMIT, DANNAH DANIELLE, GRADO', 'section_id' => $hawkingSectionId],
            ['name' => 'MANSALAPUS, MERY JUNAE, ACHA', 'section_id' => $hawkingSectionId],
            ['name' => 'MENDOZA, LEINETH JYNE, VILLAMAR', 'section_id' => $hawkingSectionId],
            ['name' => 'MIRAPLES, JIM-JAYCHELLE, ALBO', 'section_id' => $hawkingSectionId],
            ['name' => 'NICASIO, EDLYNNE JOYCE, BUZON', 'section_id' => $hawkingSectionId],
            ['name' => 'PEPITO, LATHIECIA JAMIERRA, -', 'section_id' => $hawkingSectionId],
            ['name' => 'PIMENTEL, DAPHNE LIANNE, BARAQUEL', 'section_id' => $hawkingSectionId],
            ['name' => 'RICAÑA, ANTONIA YAEL, BEREDO', 'section_id' => $hawkingSectionId],
            ['name' => 'RONQUILLO, FLOUNIE AERIELLE, MARANAN', 'section_id' => $hawkingSectionId],
            ['name' => 'SANTOS, JILLIAN ESTEPHANIE MARIE, ABUTAR', 'section_id' => $hawkingSectionId],
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
