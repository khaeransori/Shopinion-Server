<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * Customer
 *
 * @property string $id
 * @property string $user_id
 * @property string $first_name
 * @property string $last_name
 * @property string $dob
 * @property string $phone
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\Customer whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereUserId($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereLastName($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereDob($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer wherePhone($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereDeletedAt($value)
 * @property boolean $active
 * @property string $note
 * @property \Carbon\Carbon $last_visited_at
 * @method static \Illuminate\Database\Query\Builder|\Customer whereActive($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereNote($value)
 * @method static \Illuminate\Database\Query\Builder|\Customer whereLastVisitedAt($value)
 * @property string $reference_code 
 * @method static \Illuminate\Database\Query\Builder|\Customer whereReferenceCode($value)
 */
class Customer extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= false;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= false; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'user_id'		=> 'required',
		'first_name'	=> 'required|min:3',
		'last_name' 	=> 'required|min:3',
		'dob'			=> 'required',
		'phone'			=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'user_id',
		'first_name',
		'last_name',
		'phone',
		'dob',
		'active',
		'note'
	];

	public static $relationsData = array(
		'addresses' => array(self::HAS_MANY, 'CustomerAddress'),
		'user'      => array(self::BELONGS_TO, 'User'),
		'wishlist'  => array(self::BELONGS_TO_MANY, 'Product', 'table' => 'wishlists')
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
			$model->user()->delete();
		});
	}

	public function getDobAttribute($value)
	{
		return strtotime($value) * 1000;
	}

	public function getLastVisitedAtAttribute($value)
	{
		return strtotime($value) * 1000;
	}

	public static function getMaxReferenceCode()
	{
		$query          = DB::table('customers')->select(DB::raw('MAX(RIGHT(reference_code, 8)) AS max'))->first();
		$tmp            = ((int)$query->max)+1;
		$max            = sprintf("%08s", $tmp);
		$reference_code = 'CS' . $max;
		
		return $reference_code;
	}
}