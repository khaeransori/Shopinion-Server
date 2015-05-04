<?php namespace App\Core\Entities\CartProduct;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * CartProduct
 *
 * @property string $id
 * @property string $cart_id
 * @property string $product_id
 * @property string $product_attribute_id
 * @property integer $qty
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereCartId($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereProductAttributeId($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereQty($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\CartProduct whereDeletedAt($value)
 */
class CartProduct extends EloquentUuidModel {
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'cart_id'		=> 'required|max:36',
		'product_id'	=> 'required|max:36',
		'qty'			=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'cart_id',
		'product_id',
		'product_attribute_id',
		'qty'
	];

	public static $relationsData = array(
		'cart'  		=> array(self::BELONGS_TO, '\App\Core\Entities\Cart\Cart'),
		'product'  		=> array(self::BELONGS_TO, '\App\Core\Entities\Product\Product'),
		'combination'  	=> array(self::BELONGS_TO, '\App\Core\Entities\ProductAttribute\ProductAttribute', 'foreignKey' => 'product_attribute_id')
	);

	public function getQtyAttribute($value)
	{
		return (int) $value;
	}
}