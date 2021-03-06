<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Order
 *
 * @property string $id
 * @property string $customer_id
 * @property string $cart_id
 * @property string $carrier_id
 * @property string $delivery_address_id
 * @property string $invoice_address_id
 * @property string $current_state
 * @property string $message
 * @property string $payment_id
 * @property float $total_product
 * @property float $shipping_price
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Order whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereCustomerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereCartId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereCarrierId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereDeliveryAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereInvoiceAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereCurrentState($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereMessage($value)
 * @method static \Illuminate\Database\Query\Builder|\Order wherePaymentId($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereTotalProduct($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereShippingPrice($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereDeletedAt($value)
 * @property string $reference_code 
 * @property string $tracking_number 
 * @method static \Illuminate\Database\Query\Builder|\Order whereReferenceCode($value)
 * @method static \Illuminate\Database\Query\Builder|\Order whereTrackingNumber($value)
 */
class Order extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= false; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'customer_id'         => 'required',
		'cart_id'             => 'required',
		'carrier_id'          => 'required',
		'current_state'       => 'required',
		'payment_id'          => 'required',
		'total_product'       => 'required',
	);

	// Don't forget to fill this array
	protected $fillable = [
		'customer_id',
		'cart_id',
		'carrier_id',
		'delivery_address_id',
		'invoice_address_id',
		'current_state',
		'message',
		'payment_id',
		'total_product',
		'tracking_number',
		'shipping_price'
	];

	public static $relationsData = array(
		'customer'         => array(self::BELONGS_TO, 'Customer'),
		'cart'             => array(self::BELONGS_TO, 'Cart'),
		'carrier'          => array(self::BELONGS_TO, 'Carrier'),
		'delivery_address' => array(self::BELONGS_TO, 'CustomerAddress', 'foreignKey' => 'delivery_address_id'),
		'detail'	       => array(self::HAS_MANY, 'OrderDetail'),
		'history'		   => array(self::HAS_MANY, 'OrderHistory'),
		'invoice_address'  => array(self::BELONGS_TO, 'CustomerAddress', 'foreignKey' => 'invoice_address_id'),
		'state'            => array(self::BELONGS_TO, 'OrderState', 'foreignKey' => 'current_state'),
		'payment'          => array(self::BELONGS_TO, 'Payment'),
	);

	/**
     * The "booting" method of the model.
     *
     * @return void
     */
	public static function boot()
	{
		parent::boot();

		static::creating(function ($model)
		{
			$model->reference_code = $model->getMaxReferenceCode();
		});
	}

	public static function getMaxReferenceCode()
	{
		$query          = DB::table('orders')->select(DB::raw('MAX(RIGHT(reference_code, 8)) AS max'))->first();
		$tmp            = ((int)$query->max)+1;
		$max            = sprintf("%08s", $tmp);
		$reference_code = 'OD' . $max;
		
		return $reference_code;
	}

	public function getTotalProductAttribute($value)
	{
		return floatval($value);
	}

	public function getShippingPriceAttribute($value)
	{
		return floatval($value);
	}
}
