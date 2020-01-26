<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = new DateTime;
		DB::table('usuario')->insert(
			array(
				'id'             => 1,
				'login'          => 'admin',
				'avatar'         => 'admin',
				'email'          => 'nespaucar@gmail.com',
				'password'       => Hash::make('123'),
				'state'          => 'H',
				'usertype_id'    => 1,
				'created_at'     => $now,
				'updated_at'     => $now
			)
		);
    }
}
