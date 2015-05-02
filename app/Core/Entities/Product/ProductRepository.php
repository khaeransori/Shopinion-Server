<?php namespace App\Core\Entities\Product;

use Prettus\Repository\Eloquent\Repository;

class ProductRepository extends Repository
{
	function __construct(Product $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    'name' => 'like',
	    'reference_code' => 'like'
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}