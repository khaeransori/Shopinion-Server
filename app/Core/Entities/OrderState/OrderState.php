<?php namespace App\Core\Entities\OrderState;

use App\Core\Services\Repositories\EloquentUuidModel;

/**
 * OrderState
 *
 * @property string $id
 * @property string $name
 * @property boolean $delivery
 * @property boolean $shipped
 * @property boolean $paid
 * @property integer $order
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereDelivery($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereShipped($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState wherePaid($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereOrder($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereDeletedAt($value)
 * @property boolean $delivered
 * @property boolean $canceled
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereDelivered($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderState whereCanceled($value)
 */
class OrderState extends EloquentUuidModel {
	
	public $autoHydrateEntityFromInput 		= true; // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called
  	public $throwOnValidation = true;
  	public static $throwOnFind = true;
  	
	// Add your validation rules here
	public static $rules = array(
		'name'			=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'name',
		'delivery',
		'shipped',
		'paid',
		'order'
	];

}