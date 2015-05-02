<?php

class CarriersTableSeeder extends Seeder {

	public function run()
	{
		\App\Core\Entities\Carrier\Carrier::create([
			'name' => 'JNE',
			'on_store' => 0
		]);

		\App\Core\Entities\Carrier\Carrier::create([
			'name' => 'Ambil di Toko',
			'on_store' => 1
		]);
	}

}