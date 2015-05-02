<?php namespace App\Core\Entities\Attribute;

use Prettus\Repository\Eloquent\Repository;

class AttributeRepository extends Repository
{
	
	function __construct(Attribute $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}