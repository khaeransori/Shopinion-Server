<?php namespace App\Core\Entities\Order;

use Prettus\Repository\Eloquent\Repository;

class OrderRepository extends Repository
{
	
	function __construct(Order $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}