<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class ActiveCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('active', '=', '1');
		return $query;
	}
}