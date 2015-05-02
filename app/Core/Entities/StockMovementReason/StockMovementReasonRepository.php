<?php namespace App\Core\Entities\StockMovementReason;

use Prettus\Repository\Eloquent\Repository;

class StockMovementReasonRepository extends Repository
{
	
	function __construct(StockMovementReason $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}