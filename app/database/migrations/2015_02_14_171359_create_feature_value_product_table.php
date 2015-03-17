<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateFeatureValueProductTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('feature_value_product', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('feature_value_id', 36)->index();
			$table->foreign('feature_value_id')->references('id')->on('feature_values')->onDelete('cascade');
			$table->char('product_id', 36)->index();
			$table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
			$table->timestamps();
		});
	}


	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('feature_value_product');
	}

}
