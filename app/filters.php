<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
	header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT, PATCH');
    header('Access-Control-Allow-Headers: Origin, Content-Type, Accept, Authorization, X-Request-With');
    header('Access-Control-Allow-Credentials: true');
});


App::after(function($request, $response)
{
	//
	header('Access-Control-Allow-Origin: *');
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
| General HttpException handler
*/
App::error(function(Symfony\Component\HttpKernel\Exception\HttpException $e, $code)
{
	$headers = $e->getHeaders();

	switch ($code) {
		case 401:
			$default_message = 'Invalid API key';
			break;
		
		case 403:
			$default_message = 'Insufficient privileges to perform this action';
			break;

		case 404:
			$default_message = 'The requested resource was not found';
			break;

		default:
			$default_message = 'An error was ecountered';
			break;
	}

	return Response::json(array(
		'error' => true,
		'message' => $e->getMessage() ?: $default_message
		), $code, $headers);
});

/**
 * Permission Exception Handler
 */
App::error(function(Shopinion\Services\Exceptions\PermissionException $e, $code)
{
  return Response::json($e->getMessage(), $e->getCode());
});
 
/**
 * Validation Exception Handler
 */
App::error(function(Shopinion\Services\Exceptions\ValidationException $e, $code)
{
  return Response::json($e->getMessages(), $code);
});
 
/**
 * Not Found Exception Handler
 */
App::error(function(Shopinion\Services\Exceptions\NotFoundException $e)
{
  return Response::json($e->getMessage(), $e->getCode());
});

/**
 * Model Not Found Exception Handler
 */
App::error(function(Illuminate\Database\Eloquent\ModelNotFoundException $e)
{
    return Response::json(array(
    	'status_code' 	=> 404,
    	'message'		=> $e->getMessage()
    ), 404);
});

/**
 * Move Not Possible Exception Handler
 */
App::error(function(Baum\MoveNotPossibleException $e)
{
    return Response::json(array(
    	'status_code' 	=> 400,
    	'message'		=> [
    		'node' => $e->getMessage()
    	]
    ), 400);
});

App::error(function(Tymon\JWTAuth\Exceptions\JWTException $e, $code)
{
    if ($e instanceof Tymon\JWTAuth\Exceptions\TokenExpiredException) {
        return Response::json(['token_expired'], $e->getStatusCode());
    } else if ($e instanceof Tymon\JWTAuth\Exceptions\TokenInvalidException) {
        return Response::json(['token_invalid'], $e->getStatusCode());
    }
});

