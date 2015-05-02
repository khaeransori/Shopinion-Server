<?php namespace App\Core\Entities\ProductAttribute;

use Prettus\Repository\Eloquent\Repository;

class ProductAttributeRepository extends Repository
{
	
	function __construct(ProductAttribute $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}