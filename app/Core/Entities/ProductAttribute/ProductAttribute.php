<?php namespace App\Core\Entities\ProductAttribute;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * ProductAttribute
 *
 * @property string $id
 * @property string $product_id
 * @property boolean $default_on
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereDefaultOn($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductAttribute whereDeletedAt($value)
 */
class ProductAttribute extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'product_id'	=> 'required|max:36'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'product_id',
		'default_on'
	];

	public static $relationsData = array(
		'attribute_combinations'	=> array(self::BELONGS_TO_MANY, '\App\Core\Entities\Attribute\Attribute', 'table' => 'product_attribute_combinations'),
		'product'					=> array(self::BELONGS_TO, '\App\Core\Entities\Product\Product'),
		'stock'						=> array(self::HAS_ONE, '\App\Core\Entities\Stock\Stock')
	);
}