<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateWishlistsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('wishlists', function(Blueprint $table)
		{
			$table->char('id', 36);
            $table->char('product_id', 36)->index();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->char('user_id', 36)->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
		Schema::drop('wishlists');
	}

}
