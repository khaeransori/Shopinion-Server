<?php namespace App\Core\Entities\CustomerAddress;

use Prettus\Repository\Eloquent\Repository;

class CustomerAddressRepository extends Repository
{
	
	function __construct(CustomerAddress $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}