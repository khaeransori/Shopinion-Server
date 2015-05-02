<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetBySignCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('sign', '=', \Input::get('sign'));
		return $query;
	}
}