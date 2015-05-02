<?php namespace App\Core\Entities\StockMovementReason;

use Shopinion\Services\Repositories\EloquentModel;

/**
 * StockMovementReason
 *
 * @property integer $id
 * @property boolean $sign
 * @property string $name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereSign($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereUpdatedAt($value)
 * @property \Carbon\Carbon $deleted_at
 * @method static \Illuminate\Database\Query\Builder|\StockMovementReason whereDeletedAt($value)
 */
class StockMovementReason extends EloquentModel {
 
	public $throwOnValidation = true;
  	public static $throwOnFind = true;
  	
	// Add your validation rules here
	public static $rules = [
		'sign'	=> 'required',
		'name' => 'required'
	];

	// Don't forget to fill this array
	protected $fillable = ['sign', 'name'];

	public static $relationsData = array(
		'movement'  => array(self::HAS_MANY, '\App\Core\Entities\StockMovement\StockMovement')
	);
}