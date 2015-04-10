<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePaymentConfirmationsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('payment_confirmations', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('order_id', 36);
			$table->char('bank_account_id', 36);
			$table->decimal('ammount', 20, 6);
			$table->date('date_paid');
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
		Schema::drop('payment_confirmations');
	}

}
