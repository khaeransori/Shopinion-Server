<?php namespace App\Core\Entities\FeatureValue;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * FeatureValue
 *
 * @property string $id
 * @property string $feature_id
 * @property string $value
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereFeatureId($value)
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\FeatureValue whereDeletedAt($value)
 */
class FeatureValue extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'feature_id'	=> 'required|max:36',
		'value' 		=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'feature_id',
		'value'
	];

	public static $relationsData = array(
		'feature' 	=> array(self::BELONGS_TO, '\App\Core\Entities\Feature\Feature'),
		'product'	=> array(self::BELONGS_TO_MANY, '\App\Core\Entities\Product\Product')
	);
}