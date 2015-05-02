<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByManufacturerCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('manufacturer_id', '=', \Input::get('manufacturer'));
		return $query;
	}
}