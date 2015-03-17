<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductAttributeCombinationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('product_attribute_combinations', function(Blueprint $table)
		{
			$table->increments('id');
			$table->char('attribute_id', 36)->index();
			$table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
			$table->char('product_attribute_id', 36)->index();
			$table->foreign('product_attribute_id')->references('id')->on('product_attributes')->onDelete('cascade');
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
		Schema::drop('product_attribute_combinations');
	}

}