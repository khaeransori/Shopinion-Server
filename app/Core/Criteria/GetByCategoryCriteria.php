<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByCategoryCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('default_category_id', '=', \Input::get('category'));
		return $query;
	}
}