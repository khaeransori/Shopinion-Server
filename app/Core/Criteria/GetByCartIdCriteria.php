<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByCartIdCriteria implements Criteria
{
	protected $cart_id;

	function __construct($cart_id = null) {
		$this->cart_id = $cart_id;
	}

	public function apply($query, Repository $repository)
	{
		$term = (is_null($this->cart_id)) ? \Input::get('cart_id') : $this->cart_id;
		$query = $query->where('cart_id', '=', $term);
		return $query;
	}
}