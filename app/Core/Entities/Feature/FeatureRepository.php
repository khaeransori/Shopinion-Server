<?php namespace App\Core\Entities\Feature;

use Prettus\Repository\Eloquent\Repository;

class FeatureRepository extends Repository
{
	
	function __construct(Feature $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}