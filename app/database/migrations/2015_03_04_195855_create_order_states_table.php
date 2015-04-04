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
			$table->boolean('delivered')->default(false);
			$table->boolean('paid')->default(false);
			$table->boolean('canceled')->default(false);
			$table->integer('order')->default(0);
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
