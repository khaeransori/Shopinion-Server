<?php namespace App\Core\Entities\User;

use Prettus\Repository\Eloquent\Repository;

class UserRepository extends Repository
{
	
	function __construct(User $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}