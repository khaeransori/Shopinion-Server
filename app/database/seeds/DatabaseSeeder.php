<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('CarriersTableSeeder');
		$this->call('CategoriesTableSeeder');
		$this->call('OrderStatesTableSeeder');
		$this->call('PaymentsTableSeeder');
		$this->call('StockMovementReasonsTableSeeder');
		$this->call('UsersTableSeeder');
	}

}
