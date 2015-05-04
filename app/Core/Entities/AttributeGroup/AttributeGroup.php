<?php namespace App\Core\Entities\AttributeGroup;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * AttributeGroup
 *
 * @property string $id
 * @property boolean $is_color
 * @property string $name
 * @property string $public_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereIsColor($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup wherePublicName($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\AttributeGroup whereDeletedAt($value)
 */
class AttributeGroup extends EloquentUuidModel {
    
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name' 			=> 'required|min:3',
		'public_name' 	=> 'required|min:3'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'public_name',
		'is_color'
	];

	public static $relationsData = array(
		'attributes'  => array(self::HAS_MANY, '\App\Core\Entities\Attribute\Attribute')
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
			$model->attributes()->delete();
		});
	}
}