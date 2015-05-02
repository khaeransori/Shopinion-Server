<?php namespace App\Core\Entities\AttributeGroup;

use Prettus\Repository\Eloquent\Repository;

class AttributeGroupRepository extends Repository
{
	
	function __construct(AttributeGroup $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}