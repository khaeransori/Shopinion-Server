<?php namespace App\Core\Entities\Category;

use Prettus\Repository\Eloquent\Repository;

class CategoryRepository extends Repository
{
  
  function __construct(Category $model)
  {
    parent::__construct($model);
  }

  protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}