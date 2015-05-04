<?php namespace App\Core\Entities\Product;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * Product
 *
 * @property string $id
 * @property string $manufacturer_id
 * @property string $name
 * @property string $description
 * @property string $reference_code
 * @property string $ean13
 * @property string $upc
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Product whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereManufacturerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereReferenceCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereEan13($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereUpc($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Product whereDeletedAt($value)
 * @property string $default_category_id
 * @property float $price
 * @method static \Illuminate\Database\Query\Builder|\Product whereDefaultCategoryId($value)
 * @method static \Illuminate\Database\Query\Builder|\Product wherePrice($value)
 * @property float $sale_price
 * @method static \Illuminate\Database\Query\Builder|\Product whereSalePrice($value)
 * @property boolean $active
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @method static \Illuminate\Database\Query\Builder|\Product whereActive($value)
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $productStock 
 * @property-read \Stock')
 * 	    				->selectRaw('product_id $aggregateStock 
 */
class Product extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'name'				=> 'required|min:3',
		'reference_code' 	=> 'required|unique:products',
		'manufacturer_id'	=> 'max:36',
		'default_category_id' => 'max:36'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'manufacturer_id',
		'default_category_id',
		'name',
		'description',
		'reference_code',
		'price',
		'sale_price',
		'active'
	];

	public static $relationsData = array(
		'category'		=> array(self::HAS_ONE, '\App\Core\Entities\Category\Category', 'foreignKey' => 'id', 'localKey' => 'default_category_id'),
		'categories'	=> array(self::BELONGS_TO_MANY, '\App\Core\Entities\Category\Category'),
		'combinations'	=> array(self::HAS_MANY, '\App\Core\Entities\ProductAttribute\ProductAttribute'),
		'features'		=> array(self::BELONGS_TO_MANY, '\App\Core\Entities\FeatureValue\FeatureValue'),
		'images'		=> array(self::HAS_MANY, '\App\Core\Entities\ProductImage\ProductImage'),
		'manufacturer'  => array(self::BELONGS_TO, '\App\Core\Entities\Manufacturer\Manufacturer'),
		'wishlist'		=> array(self::BELONGS_TO_MANY, '\App\Core\Entities\Customer\Customer', 'table' => 'wishlists')
	);

	public function productStock()
	{
	    return $this->hasOne('App\Core\Entities\Stock\Stock')
	    				->selectRaw('product_id, sum(qty) as qty')
	    				->where('product_attribute_id', '=', 0)
	    				->groupBy('product_id');
	}

	public function aggregateStock()
	{
	    return $this->hasOne('App\Core\Entities\Stock\Stock')
	    				->selectRaw('product_id, sum(qty) as qty')
	    				->where('product_attribute_id', '!=', 0)
	    				->groupBy('product_id');
	}
}
