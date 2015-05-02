<?php namespace App\Core\Entities\Carrier;

use Prettus\Repository\Eloquent\Repository;

class CarrierRepository extends Repository
{
	
	function __construct(Carrier $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}