<?php namespace App\Core\Entities\Permission;

use Prettus\Repository\Eloquent\Repository;

class PermissionRepository extends Repository
{
	
	function __construct(Permission $model)
	{
		parent::__construct($model);
	}
}