<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByProductAttributeIdCriteria implements Criteria
{
	
	public function apply($query, Repository $repository)
	{
		$query = $query->where('product_attribute_id', '=', \Input::get('product_attribute_id'));
		return $query;
	}
}