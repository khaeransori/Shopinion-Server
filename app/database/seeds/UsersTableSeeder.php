<?php

use Rhumsaa\Uuid\Uuid;

class UsersTableSeeder extends Seeder {

	public function run()
	{
		\App\Core\Entities\User\User::create([
			'id' 					=> Uuid::uuid4(),
			'email'                 => 'administrator',
			'password'              => 'administrator',
			'password_confirmation' => 'administrator',
			'confirmation_code'     => md5(uniqid(mt_rand(), true)),
			'confirmed'             => 1,
			'is_customer'			=> 0
		]);
	}
}