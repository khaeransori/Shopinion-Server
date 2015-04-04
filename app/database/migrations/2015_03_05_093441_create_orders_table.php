<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('orders', function(Blueprint $table)
		{
			$table->char('id', 36);
			$table->char('reference_code', 10);
			$table->char('customer_id', 36);
			$table->char('cart_id', 36);
			$table->char('carrier_id', 36);
			$table->char('delivery_address_id', 36)->nullable();
			$table->char('invoice_address_id', 36)->nullable();
			$table->char('current_state', 36);
			$table->longText('message')->nullable();
			$table->char('payment_id', 36);
			$table->decimal('total_product', 20, 6);
			$table->decimal('shipping_price', 20, 6)->nullable();
			$table->string('tracking_number')->nullable();
			$table->boolean('delivered')->default(0);
			$table->boolean('paid')->default(0);
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
		Schema::drop('orders');
	}

}
