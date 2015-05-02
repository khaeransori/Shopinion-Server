<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class IsAdministratorCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('is_customer', '=', '0');
		return $query;
	}
}