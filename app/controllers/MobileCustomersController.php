<?php

class MobileCustomersController extends \BaseController {

	function __construct(Customer $repo, User $user, REST $rest) {
		$this->user = $user;
		$this->repo = $repo;
		$this->rest = $rest;
	}

	public function forgotPassword()
	{
		if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            $response = array('message' => array('notice' => $notice_msg));
            return $this->rest->response(200, $response, false);
        }

        $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
        $response = array('message' => array('error' => $error_msg));
        return $this->rest->response(200, $response, false);
	}
	
	public function login()
	{
		// grab credentials from the request
	    $credentials = Input::only('email', 'password');

	    try {
	        // attempt to verify the credentials and create a token for the user
	        if (! $token = JWTAuth::attempt($credentials)) {
	        	$response = array('message' => array('account' => 'Invalid credentials'));
	        	return $this->response->error($response, 401);
	        }
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        // something went wrong whilst attempting to encode the token
	        $response = array('message' => array('account' => 'Could not create token'));
	        return $this->response->error($response, 500);
	    }

		$customer = $this->repo->whereHas('user', function ($user)
		{
			$user->where('email', Input::get('email'));
		})->get()->first();
		$wishlist_count = $customer->wishlist->count();

	    $response = array(
			'account' 			=> $customer,
			'token'   			=> $token,
			'wishlist_count'	=> $wishlist_count
	    	);
	    return $this->rest->response(200, $response, false);
	}

	public function register()
	{
		$real_dob = explode('/', Input::get('dob'));

		DB::beginTransaction();
		
		$user                        = new $this->user;
		$user->email                 = Input::get('email');
		$user->password              = Input::get('password');
		$user->password_confirmation = Input::get('password');
		$user->confirmation_code     = md5(uniqid(mt_rand(), true));
		$user->confirmed             = 1;

		if ($user->save()) {
			
			$customer             = new $this->repo;
			$customer->first_name = Input::get('first_name');
			$customer->last_name  = Input::get('last_name');
			$customer->dob 		  = $real_dob[2].'-'.$real_dob[1].'-'.$real_dob;
			$customer->phone      = Input::get('phone');
			$customer->user_id    = $user->id;

			if ($customer->save()) {
				DB::commit();

				$customer->load('user');

				return $this->rest->response(201, $customer);
			}

			DB::rollBack();
			return $this->response->errorBadRequest($customer->errors());
		}

		return $this->response->errorBadRequest($user->errors());
	}

}
