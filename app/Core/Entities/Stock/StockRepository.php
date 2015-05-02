<?php namespace App\Core\Entities\Stock;

use Prettus\Repository\Eloquent\Repository;

class StockRepository extends Repository
{
	
	function __construct(Stock $model)
	{
		parent::__construct($model);
	}
}