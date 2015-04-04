<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateAttributeGroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('attribute_groups', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->boolean('is_color')->default(0);
			$table->string('name');
			$table->string('public_name');
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
		Schema::drop('attribute_groups');
	}

}
