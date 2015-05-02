<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class OrderedCriteria implements Criteria
{
	protected $ordered;

	function __construct($ordered = null) {
		$this->ordered = $ordered;
	}

	public function apply($query, Repository $repository)
	{
		$term = (is_null($this->ordered)) ? \Input::get('ordered') : $this->ordered;
		$query = $query->where('ordered', '=', $term);
		return $query;
	}
}