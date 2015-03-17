<?php

use Shopinion\Services\Repositories\EloquentUuidModel;


/**
 * OrderHistory
 *
 * @property integer $id 
 * @property string $order_id 
 * @property string $order_state_id 
 * @property \Carbon\Carbon $created_at 
 * @property \Carbon\Carbon $updated_at 
 * @property-read mixed $deleted_at 
 * @method static \Illuminate\Database\Query\Builder|\OrderHistory whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderHistory whereOrderId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderHistory whereOrderStateId($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderHistory whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\OrderHistory whereUpdatedAt($value)
 */
class OrderHistory extends EloquentUuidModel {

	// Add your validation rules here
	public static $rules = array(
		'order_id'       => 'required',
		'order_state_id' => 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'order_id',
		'order_state_id'
	];

	public static $relationsData = array(
		'order' => array(self::BELONGS_TO, 'Order'),
		'state' => array(self::BELONGS_TO, 'OrderState', 'foreignKey' => 'order_state_id')
	);


}
