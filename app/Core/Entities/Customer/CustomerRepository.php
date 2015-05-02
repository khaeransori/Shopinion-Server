<?php namespace App\Core\Entities\Customer;

use Prettus\Repository\Eloquent\Repository;

class CustomerRepository extends Repository
{
	function __construct(Customer $model)
	{
		parent::__construct($model);
	}
	
	protected $fieldSearchable = [
	    'first_name' => 'like',
	    'last_name' => 'like'
	];


	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}