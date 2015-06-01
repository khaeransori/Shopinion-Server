<?php namespace App\Core\Entities\PaymentConfirmation;

use Carbon\Carbon;
use App\Core\Services\Repositories\EloquentUuidModel;

class PaymentConfirmation extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= false; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'order_id'		  => 'required|max:36',
		'bank_account_id' => 'required|max:36',
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
		'order'		=> array(self::BELONGS_TO, '\App\Core\Entities\Order\Order'),
		'bank'		=> array(self::HAS_ONE, '\App\Core\Entities\BankAccount\BankAccount', 'foreignKey' => 'id', 'localKey' => 'bank_account_id')
	);

	public function getDatePaidAttribute($value)
	{
		$dt = new Carbon($value);
	    $datetime = $dt->toIso8601String();

	    return $datetime;
	}

	public function getRules()
	{
		return self::$rules;
	}
}