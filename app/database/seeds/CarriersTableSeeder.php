<?php

class CarriersTableSeeder extends Seeder {

	public function run()
	{
		Carrier::create([
			'name' => 'JNE',
			'on_store' => 0
		]);

		Carrier::create([
			'name' => 'Ambil di Toko',
			'on_store' => 1
		]);
	}

}