<?php namespace App\Core\Entities\FeatureValue;

use Prettus\Repository\Eloquent\Repository;

class FeatureValueRepository extends Repository
{
	
	function __construct(FeatureValue $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}