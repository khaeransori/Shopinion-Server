<?php namespace App\Core\Entities\OrderHistory;

use Prettus\Repository\Eloquent\Repository;

class OrderHistoryRepository extends Repository
{
	
	function __construct(OrderHistory $model)
	{
		parent::__construct($model);
	}
}