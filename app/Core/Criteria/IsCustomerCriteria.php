<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class IsCustomerCriteria implements Criteria
{
	protected $is_customer;

	function __construct($is_customer = null) {
		$this->is_customer = $is_customer;
	}

	public function apply($query, Repository $repository)
	{
		$term = (is_null($this->is_customer)) ? \Input::get('is_customer') : $this->is_customer;
		$query = $query->where('is_customer', '=', $term);
		return $query;
	}
}