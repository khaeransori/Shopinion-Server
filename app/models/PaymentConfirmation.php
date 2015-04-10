<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

class PaymentConfirmation extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= false; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'order_id'		  => 'required',
		'bank_account_id' => 'required',
		'ammount'         => 'required',
		'date_paid'       => 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'order_id',
		'bank_account_id',
		'ammount',
		'date_paid'
	];

	public static $relationsData = array(
		'order'      => array(self::BELONGS_TO, 'Order'),
		'payment'      => array(self::BELONGS_TO, 'Payment')
	);

	public function getDatePaidAttribute($value)
	{
		return strtotime($value) * 1000;
	}
}