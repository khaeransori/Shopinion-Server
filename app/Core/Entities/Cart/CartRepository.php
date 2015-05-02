<?php namespace App\Core\Entities\Cart;

use Prettus\Repository\Eloquent\Repository;

class CartRepository extends Repository
{
	
	function __construct(Cart $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}