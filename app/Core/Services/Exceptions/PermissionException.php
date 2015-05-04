<?php namespace App\Core\Services\Exceptions;
 
class PermissionException extends \Exception
{
	
	function __construct($message = null, $code = 403)
	{
		parent::__construct($message ?: 'Action not allowed', $code);
	}

	
}