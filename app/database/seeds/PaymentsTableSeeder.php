<?php

class PaymentsTableSeeder extends Seeder {

	public function run()
	{
		Payment::create([
			'name' => 'At Store',
			'is_cod' => 1
		]);

		Payment::create([
			'name' => 'Bank Wire',
			'is_cod' => 0
		]);
	}

}