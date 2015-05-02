<?php namespace App\Core\Entities\Cart;

use Shopinion\Services\Repositories\EloquentUuidModel;
use Illuminate\Support\Facades\DB;

/**
 * Cart
 *
 * @property integer $id
 * @property string $carrier_id
 * @property string $delivery_address_id
 * @property string $invoice_address_id
 * @property string $customer_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Cart whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereCarrierId($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereDeliveryAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereInvoiceAddressId($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereCustomerId($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereDeletedAt($value)
 * @property boolean $is_customer
 * @property boolean $ordered
 * @method static \Illuminate\Database\Query\Builder|\Cart whereIsCustomer($value)
 * @method static \Illuminate\Database\Query\Builder|\Cart whereOrdered($value)
 * @property string $reference_code 
 * @method static \Illuminate\Database\Query\Builder|\Cart whereReferenceCode($value)
 */
class Cart extends EloquentUuidModel {
	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
	public $throwOnValidation = true;
  	public static $throwOnFind = true;

	// Add your validation rules here
	public static $rules = array(
		'customer_id'	=> 'required|max:36',
	);

	// Don't forget to fill this array
	protected $fillable = [
		'carrier_id',
		'customer_id',
		'delivery_address_id',
		'invoice_address_id',
		'is_customer',
		'ordered'
	];

	public static $relationsData = array(
		'products'  => array(self::HAS_MANY, '\App\Core\Entities\CartProduct\CartProduct'),
		'customer'	=> array(self::BELONGS_TO, '\App\Core\Entities\Customer\Customer'),
		'carrier'	=> array(self::BELONGS_TO, '\App\Core\Entities\Carrier\Carrier'),
		'delivery_address' => array(self::BELONGS_TO, '\App\Core\Entities\CustomerAddress\CustomerAddress', 'foreignKey' => 'delivery_address_id'),
		'invoice_address' => array(self::BELONGS_TO, '\App\Core\Entities\CustomerAddress\CustomerAddress', 'foreignKey' => 'invoice_address_id'),
		'order' => array(self::HAS_ONE, '\App\Core\Entities\Order\Order')
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

		static::deleted(function ($model)
		{
			$model->products()->delete();
		});
	}

	public static function getMaxReferenceCode()
	{
		$query          = DB::table('carts')->select(DB::raw('MAX(RIGHT(reference_code, 8)) AS max'))->first();
		$tmp            = ((int)$query->max)+1;
		$max            = sprintf("%08s", $tmp);
		$reference_code = 'CR' . $max;
		
		return $reference_code;
	}
}