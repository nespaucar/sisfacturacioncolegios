<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now          = new DateTime;
		$list         = DB::table('menuoption')->get();
		$i = 0;
		foreach ($list as $key => $value) {
			$i++;
			DB::table('permission')->insert(
				array(
					array(
						'id'            => $i,
						'usertype_id'   => 1,
						'menuoption_id' => $value->id,
						'created_at'    => $now,
						'updated_at'    => $now
					)
				)
			);
		}
    }
}
