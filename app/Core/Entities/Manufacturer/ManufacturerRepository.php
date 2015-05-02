<?php namespace App\Core\Entities\Manufacturer;

use Prettus\Repository\Eloquent\Repository;

class ManufacturerRepository extends Repository
{
	
	function __construct(Manufacturer $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}