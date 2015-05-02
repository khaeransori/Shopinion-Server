<?php namespace App\Core\Entities\ProductImage;

use Prettus\Repository\Eloquent\Repository;

class ProductImageRepository extends Repository
{
	
	function __construct(ProductImage $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}