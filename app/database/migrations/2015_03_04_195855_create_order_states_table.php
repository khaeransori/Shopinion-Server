<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderStatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_states', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->string('name');
			$table->boolean('delivered');
			$table->boolean('paid');
			$table->boolean('canceled');
			$table->integer('order');
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
		Schema::drop('order_states');
	}

}
