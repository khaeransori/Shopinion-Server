<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

class BankAccount extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'name'		=> 'required',
		'account'	=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'account'
	];

	public static $relationsData = array(
		
	);
}