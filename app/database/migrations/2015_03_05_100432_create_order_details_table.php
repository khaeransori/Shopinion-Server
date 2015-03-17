<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrderDetailsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('order_details', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('order_id', 36);
			$table->char('product_id', 36);
			$table->char('product_attribute_id', 36);
			$table->longText('product_name');
			$table->string('product_reference');
			$table->integer('product_quantity');
			$table->decimal('product_price', 20, 6);
			$table->decimal('total_price', 20, 6);
			$table->decimal('original_product_price', 20, 6);
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
		Schema::drop('order_details');
	}

}
