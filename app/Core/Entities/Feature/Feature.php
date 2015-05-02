<?php namespace App\Core\Entities\Feature;

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Feature
 *
 * @property string $id
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Feature whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Feature whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Feature whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Feature whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Feature whereDeletedAt($value)
 */
class Feature extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;
  	
	// Add your validation rules here
	public static $rules = array(
		'name' 			=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name'
	];

	public static $relationsData = array(
		'values'  => array(self::HAS_MANY, 'App\Core\Entities\FeatureValue\FeatureValue')
	);

	/**
     * The "booting" method of the model.
     *
     * @return void
     */
	public static function boot()
	{
		parent::boot();

		static::deleted(function ($model)
		{
			$model->values()->delete();
		});
	}
}