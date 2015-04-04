<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Manufacturer
 *
 * @property string $id
 * @property string $name
 * @property string $description
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereDeletedAt($value)
 * @property boolean $active
 * @method static \Illuminate\Database\Query\Builder|\Manufacturer whereActive($value)
 */
class Manufacturer extends EloquentUuidModel {
    
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'description' 	=> 'required',
		'name'			=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'active',
		'description',
		'name'
	];

	public static $relationsData = array(
		'products'  => array(self::HAS_MANY, 'Product')
	);
}