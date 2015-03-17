<?php

use Shopinion\Services\Repositories\EloquentUuidModel;


/**
 * CustomerAddress
 *
 * @property string $id
 * @property string $customer_id
 * @property string $alias
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $phone
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereCustomerId($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereAlias($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereAddress($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\CustomerAddress whereDeletedAt($value)
 */
class CustomerAddress extends EloquentUuidModel {
    
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'customer_id'		=> 'required',
		'alias'				=> 'required|min:3',
		'first_name'		=> 'required|min:3',
		'last_name'			=> 'required|min:3',
		'address'			=> 'required|min:3',
		'phone'				=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'customer_id',
		'alias',
		'first_name',
		'last_name',
		'address',
		'phone'
	];

	public static $relationsData = array(
		'customer'		=> array(self::BELONGS_TO, 'Manufacturer')
	);
}