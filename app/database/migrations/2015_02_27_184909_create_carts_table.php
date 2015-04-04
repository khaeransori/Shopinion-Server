<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('carts', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('reference_code', 10);
			$table->char('carrier_id', 36)->nullable();
			$table->char('delivery_address_id', 36)->nullable();
			$table->char('invoice_address_id', 36)->nullable();
			$table->char('customer_id', 36);
			$table->boolean('is_customer')->default(0);
			$table->boolean('ordered')->default(0);
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
		Schema::drop('carts');
	}

}
