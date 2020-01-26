<?php

use Illuminate\Database\Seeder;

class PersonaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$now = new DateTime;
        DB::table('persona')->insert(
        	array(
				array(
					'id'              => 1,
					'nombres'         => "Néstor Alexander",
					'apellidopaterno' => "Paucar",
					'apellidomaterno' => "Carhuatanta",
					'dni'     		  => "73700450",
					'direccion'       => "Calle Circunvalación 150 - Chiclayo",
					'created_at'      => $now,
					'updated_at'      => $now
				)
			)
		);
    }
}
