<?php namespace App\Core\Entities\Role;

use Prettus\Repository\Eloquent\Repository;

class RoleRepository extends Repository
{
	
	function __construct(Role $model)
	{
		parent::__construct($model);
	}
}