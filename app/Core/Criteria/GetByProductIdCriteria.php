<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByProductIdCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('product_id', '=', \Input::get('product_id'));
		return $query;
	}
}