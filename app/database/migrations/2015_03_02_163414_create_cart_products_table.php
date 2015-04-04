<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateCartProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cart_products', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('cart_id', 36);
			$table->char('product_id', 36);
			$table->char('product_attribute_id', 36)->default(0);
			$table->integer('qty');
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
		Schema::drop('cart_products');
	}

}
