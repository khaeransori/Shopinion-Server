<?php namespace App\Core\Entities\Payment;

use Prettus\Repository\Eloquent\Repository;

class PaymentRepository extends Repository
{
	
	function __construct(Payment $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}