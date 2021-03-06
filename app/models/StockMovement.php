<?php

use Shopinion\Services\Repositories\EloquentUuidModel;

/**
 * StockMovement
 *
 * @property string $id
 * @property string $stock_id
 * @property integer $stock_movement_reason_id
 * @property integer $qty
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereStockId($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereStockMovementReasonId($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereQty($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovement whereDeletedAt($value)
 */
class StockMovement extends EloquentUuidModel {

	public $autoHydrateEntityFromInput 		= true;    // hydrates on new entries' validation
  	public $forceEntityHydrationFromInput 	= true; // hydrates whenever validation is called

	// Add your validation rules here
	public static $rules = array(
		'stock_id' 					=> 'required|max:36',
		'stock_movement_reason_id' 	=> 'required',
		'qty'						=> 'required'
	);

	// Don't forget to fill this array
	protected $fillable = [
		'stock_id',
		'stock_movement_reason_id',
		'qty'	
	];

	public static $relationsData = array(
		'stock'  => array(self::BELONGS_TO, 'Stock'),
		'reason' => array(self::BELONGS_TO, 'StockMovementReason', 'foreignKey' => 'stock_movement_reason_id')
	);

}