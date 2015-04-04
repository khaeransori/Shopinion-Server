<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCustomersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('customers', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('reference_code', 10);
			$table->char('user_id', 36);
			$table->string('first_name');
			$table->string('last_name');
			$table->date('dob');
			$table->string('phone');
			$table->longText('note')->nullable();
			$table->boolean('active')->default(false);
			$table->datetime('last_visited_at')->nullable();
			$table->timestamps();
			$table->softDeletes();
			$table->primary('id');
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('customers');
	}

}
