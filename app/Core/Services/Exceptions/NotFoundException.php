<?php namespace App\Core\Services\Exceptions;

class NotFoundException extends \Exception
{
	function __construct($message = null, $code = 404)
	{
		parent::__construct($message ?: 'Resource Not Found', $code);
	}
}