<?php namespace App\Core\Entities\StockMovement;

use Prettus\Repository\Eloquent\Repository;

class StockMovementRepository extends Repository
{
	
	function __construct(StockMovement $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}