<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByCustomerIdCriteria implements Criteria
{
	
	protected $customer_id;
	function __construct($customer_id = null) {
		$this->customer_id = $customer_id;
	}

	public function apply($query, Repository $repository)
	{
		$term = (is_null($this->customer_id)) ? \Input::get('customer_id') : $this->customer_id;
		$query = $query->where('customer_id', '=', $term);
		return $query;
	}
}