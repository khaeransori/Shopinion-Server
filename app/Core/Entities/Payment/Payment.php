<?php namespace App\Core\Entities\Payment;

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Payment
 *
 * @property string $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Payment whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Payment whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Payment whereDeletedAt($value)
 */
class Payment extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name'			=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'is_cod'
	];
}