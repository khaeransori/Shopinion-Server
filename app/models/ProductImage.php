<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * ProductImage
 *
 * @property string $id
 * @property string $product_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\ProductImage whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductImage whereProductId($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\ProductImage whereUpdatedAt($value)
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\ProductImage whereDeletedAt($value)
 * @property-read mixed $default_source
 * @property-read mixed $cart_source
 * @property-read mixed $small_source
 * @property-read mixed $medium_source
 * @property-read mixed $large_source
 */
class ProductImage extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called

  	protected $appends = ['default_source', 'cart_source', 'small_source', 'medium_source', 'large_source'];
	// Add your validation rules here
	public static $rules = array(
		'product_id'	=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'product_id',
	];

	public static $relationsData = array(
		'product'	=> array(self::BELONGS_TO, 'Product')
	);

	public function getDefaultSourceAttribute()
	{
		return url('/images/product') . '/' . $this->attributes['product_id'] . '/' . $this->attributes['id'] . '/' . $this->attributes['id'] . '.jpg';
	}

	public function getCartSourceAttribute()
	{
		return url('/images/product') . '/' . $this->attributes['product_id'] . '/' . $this->attributes['id'] . '/' . $this->attributes['id'] . '-cart_default.jpg';
	}

	public function getSmallSourceAttribute()
	{
		return url('/images/product') . '/' . $this->attributes['product_id'] . '/' . $this->attributes['id'] . '/' . $this->attributes['id'] . '-small_default.jpg';
	}

	public function getMediumSourceAttribute()
	{
		return url('/images/product') . '/' . $this->attributes['product_id'] . '/' . $this->attributes['id'] . '/' . $this->attributes['id'] . '-medium_default.jpg';
	}

	public function getLargeSourceAttribute()
	{
		return url('/images/product') . '/' . $this->attributes['product_id'] . '/' . $this->attributes['id'] . '/' . $this->attributes['id'] . '-large_default.jpg';
	}
}