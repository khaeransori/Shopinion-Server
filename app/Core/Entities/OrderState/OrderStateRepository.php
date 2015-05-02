<?php namespace App\Core\Entities\OrderState;

use Prettus\Repository\Eloquent\Repository;

class OrderStateRepository extends Repository
{
	
	function __construct(OrderState $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}