<?php namespace App\Core\Entities\BankAccount;

use Prettus\Repository\Eloquent\Repository;

class BankAccountRepository extends Repository
{
	
	function __construct(BankAccount $model)
	{
		parent::__construct($model);
	}

	protected $fieldSearchable = [
	    
	];

	public function boot(){
        $this->pushCriteria(app('Prettus\Repository\Criteria\RequestCriteria'));
    }
}