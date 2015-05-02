<?php namespace App\Core\Entities\PaymentConfirmation;

use Prettus\Repository\Eloquent\Repository;

class PaymentConfirmationRepository extends Repository
{
	
	function __construct(PaymentConfirmation $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}