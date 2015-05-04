<?php namespace App\Core\Entities\OrderDetail;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * OrderDetail
 *
 * @property string $id
 * @property string $order_id
 * @property string $product_id
 * @property string $product_attribute_id
 * @property string $product_name
 * @property string $product_reference
 * @property integer $product_quantity
 * @property float $product_price
 * @property float $total_price
 * @property float $original_product_price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductAttributeId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductName($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductReference($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductQuantity($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereProductPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereTotalPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereOriginalProductPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderDetail whereDeletedAt($value)
 */
class OrderDetail extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= false; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;
  	
	// Add your validation rules here
	public static $rules = array(
		'order_id'               => 'required|max:36',
		'product_id'             => 'required|max:36',
		'product_name'           => 'required',
		'product_reference'      => 'required',
		'product_quantity'       => 'required',
		'product_price'          => 'required',
		'total_price'            => 'required',
		'original_product_price' => 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'order_id',
		'product_id',
		'product_attribute_id',
		'product_name',
		'product_reference',
		'product_quantity',
		'product_price',
		'total_price',
		'original_product_price'
	];

	public static $relationsData = array(
		'product' => array(self::BELONGS_TO, 'App\Core\Entities\Product\Product')
	);


}
