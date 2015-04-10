<?php

class OrderStatesTableSeeder extends Seeder {

	public function run()
	{
		OrderState::create([
			'name'      => 'Menunggu Biaya Kirim',
			'delivered' => 0,
			'paid'      => 0,
			'canceled'  => 0,
			'order'     => 1
		]);

		OrderState::create([
			'name'      => 'Batal',
			'delivered' => 0,
			'paid'      => 0,
			'canceled'  => 1,
			'order'     => 1
		]);

		OrderState::create([
			'name'      => 'Menunggu Pembayaran',
			'delivered' => 0,
			'paid'      => 0,
			'canceled'  => 0,
			'order'     => 2
		]);

		OrderState::create([
			'name'      => 'Menunggu Konfirmasi Telah Dibayar',
			'delivered' => 0,
			'paid'      => 0,
			'canceled'  => 0,
			'order'     => 3
		]);

		OrderState::create([
			'name'      => 'Sedang dalam proses pengiriman',
			'delivered' => 0,
			'paid'      => 1,
			'canceled'  => 0,
			'order'     => 4
		]);

		OrderState::create([
			'name'      => 'Terkirim',
			'delivered' => 1,
			'paid'      => 0,
			'canceled'  => 0,
			'order'     => 5
		]);

		OrderState::create([
			'name'      => 'Finished',
			'delivered' => 0,
			'paid'      => 0,
			'canceled'  => 0,
			'order'     => 6
		]);
	}

}