<?php namespace App\Core\Entities\BankAccount;

use Shopinion\Services\Repositories\EloquentUuidModel;

class BankAccount extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name'		=> 'required|min:3',
		'account'	=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'account'
	];

	public static $relationsData = array(
		
	);
}