<?php namespace App\Core\Entities\OrderDetail;

use Prettus\Repository\Eloquent\Repository;

class OrderDetailRepository extends Repository
{
	
	function __construct(OrderDetail $model)
	{
		parent::__construct($model);
	}
}