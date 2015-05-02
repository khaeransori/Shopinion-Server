<?php

class StockMovementReasonsTableSeeder extends Seeder {

	public function run()
	{
		\App\Core\Entities\StockMovementReason\StockMovementReason::create([
			'sign' => -1,
			'name' => 'Decrease'
		]);

		\App\Core\Entities\StockMovementReason\StockMovementReason::create([
			'sign' => 1,
			'name' => 'Increase'
		]);

		\App\Core\Entities\StockMovementReason\StockMovementReason::create([
			'sign' => -1,
			'name' => 'Customer Order'
		]);
	}
}