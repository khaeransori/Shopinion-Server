<?php

class PaymentsTableSeeder extends Seeder {

	public function run()
	{
		\App\Core\Entities\Payment\Payment::create([
			'name' => 'At Store',
			'is_cod' => 1
		]);

		\App\Core\Entities\Payment\Payment::create([
			'name' => 'Bank Wire',
			'is_cod' => 0
		]);
	}

}