<?php

class CategoriesTableSeeder extends Seeder {

	public function run()
	{
		Category::create([
			'name'        => 'Home',
			'description' => 'Home Category',
			'active'      => 1
		]);
	}

}