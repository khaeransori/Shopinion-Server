<?php namespace App\Core\Criteria;

use Prettus\Repository\Contracts\Criteria;
use Prettus\Repository\Contracts\Repository;

class GetByCategoriesCriteria implements Criteria
{
	protected $categories;
	function __construct($categories = null) {
		$this->categories = $categories;
	}
	public function apply($query, Repository $repository)
	{
		if (is_array($this->categories) && !is_null($this->categories)) {
			$categories = $this->categories;
			$query = $query->whereHas('categories', function ($query) use ($categories)
			{
				$query->whereIn('category_id', $categories);
			});
		}
		return $query;
	}
}