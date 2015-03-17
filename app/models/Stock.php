<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Stock
 *
 * @property string $id
 * @property string $product_id
 * @property string $product_attribute_id
 * @property integer $qty
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Stock whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereProductAttributeId($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereQty($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Stock whereDeletedAt($value)
 */
class Stock extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= false;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'product_id' 				=> 'required|max:36',
		'product_attribute_id' 		=> 'max:36',
		'qty'						=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'product_id',
		'product_attribute_id',
		'qty'	
	];

	public static $relationsData = array(
		'product'  		=> array(self::BELONGS_TO, 'Product'),
		'combination'	=> array(self::BELONGS_TO, 'ProductAttribute', 'foreignKey' => 'product_attribute_id')
	);

	public function getQtyAttribute($value)
	{
		return (int) $value;
	}
}