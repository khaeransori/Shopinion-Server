<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class SaleCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('sale_price', '!=', '0.000000');
		return $query;
	}
}