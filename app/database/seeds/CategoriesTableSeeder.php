<?php

class CategoriesTableSeeder extends Seeder {

	public function run()
	{
		\App\Core\Entities\Category\Category::create([
			'name'        => 'Home',
			'description' => 'Home Category',
			'active'      => 1
		]);
	}

}