<?php namespace App\Core\Entities\CartProduct;

use Prettus\Repository\Eloquent\Repository;

class CartProductRepository extends Repository
{
	
	function __construct(CartProduct $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}