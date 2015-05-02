<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class DeliveredCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('delivered', '=', '1');
		return $query;
	}
}