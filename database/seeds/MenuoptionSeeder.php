<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuoptionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;

		$datos = array(
			array(
				'id'   => 1,
				'name' => 'Categoría de opción de menu',
				'link' => 'categoriaopcionmenu'
			),
			array(
				'id'   => 2,
				'name' => 'Opción de menu',
				'link' => 'opcionmenu'
			),
			array(
				'id'   => 3,
				'name' => 'Tipos de usuario',
				'link' => 'tipousuario'
			),
			array(
				'id'   => 4,
				'name' => 'Usuario',
				'link' => 'usuario'
			)
		);

		for ($i=0; $i < count($datos); $i++) { 
			DB::table('menuoption')->insert(
				array(
					'id'                    => $datos[$i]['id'],
					'name'                  => $datos[$i]['name'],
					'link'                  => $datos[$i]['link'],
					'order'                 => $i+1,
					'menuoptioncategory_id' => 1,
					'created_at'            => $now,
					'updated_at'            => $now
				)
			);
		}
    }
}
