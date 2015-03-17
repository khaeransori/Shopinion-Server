<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateProductsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('products', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('manufacturer_id', 36)->nullable();
			$table->string('name');
			$table->longText('description')->nullable();
			$table->string('reference_code', 32)->unique();
			$table->string('ean13', 13)->nullable();
			$table->string('upc', 12)->nullable();
			$table->decimal('price', 20, 6)->default(0);
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
		Schema::drop('products');
	}

}
