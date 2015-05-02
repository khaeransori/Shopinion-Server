<?php namespace App\Core\Entities\Carrier;

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Carrier
 *
 * @property string $id
 * @property string $name
 * @property boolean $on_store
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereOnStore($value)
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Carrier whereDeletedAt($value)
 */
class Carrier extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name' 					=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'on_store'
	];
}