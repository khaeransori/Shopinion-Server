<?php namespace App\Core\Entities\Attribute;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * Attribute
 *
 * @property string $id
 * @property string $attribute_group_id
 * @property string $name
 * @property string $color
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereAttributeGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereColor($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Attribute whereDeletedAt($value)
 */
class Attribute extends EloquentUuidModel {
    
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name' 					=> 'required',
		'attribute_group_id' 	=> 'required|max:36'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'attribute_group_id',
		'color'
	];

	public static $relationsData = array(
		'group'  => array(self::BELONGS_TO, '\App\Core\Entities\AttributeGroup\AttributeGroup', 'foreignKey' => 'attribute_group_id')
	);
}