<?php

class StockMovementReasonsTableSeeder extends Seeder {

	public function run()
	{
		StockMovementReason::create([
			'sign' => -1,
			'name' => 'Decrease'
		]);

		StockMovementReason::create([
			'sign' => 1,
			'name' => 'Increase'
		]);

		StockMovementReason::create([
			'sign' => -1,
			'name' => 'Customer Order'
		]);
	}
}