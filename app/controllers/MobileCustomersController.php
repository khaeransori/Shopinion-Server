<?php

class MobileCustomersController extends \BaseController {

	function __construct(Category $category, Customer $repo, User $user, REST $rest) {
		$this->category = $category;
		$this->user     = $user;
		$this->repo     = $repo;
		$this->rest     = $rest;
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
	
	public function init()
	{
		$token = Input::get('token', FALSE);

		$root = $this->category->root();
		$root->load([
			'children' => function ($children)
			{
				$children->whereActive(1)
							->orderBy('name', 'ASC');
			}
			]);

		$response['category'] = $root;

		if ($token !== FALSE) {
			JWTAuth::setToken($token);
			try {
		        if (JWTAuth::authenticate($token)) {
		        	$customer = $this->repo
						->where('active', 1)
						->whereHas('user', function ($query)
						{
							$query->where('email', Input::get('email'));
						})
						->get()
						->first();

					if ($customer->count() > 0) {
						$wishlist_count = $customer->wishlist->count();

						$token = JWTAuth::fromUser($customer->user);
						$response['account'] = $customer;
						$response['token']   = $token;
						$response['wishlist_count'] = $wishlist_count;
					}
		        }
		    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				$customer = $this->repo
					->where('active', 1)
					->whereHas('user', function ($query)
					{
						$query->where('email', Input::get('email'));
					})
					->get()
					->first();
				if ($customer->count() > 0) {
					$wishlist_count = $customer->wishlist->count();

					$token = JWTAuth::fromUser($customer->user);
					$response['account'] = $customer;
					$response['token']   = $token;
					$response['wishlist_count'] = $wishlist_count;
				}
		    } catch (Exception $e) {
		    }
		}

		return $this->rest->response(200, $response, false);
	}

	public function login()
	{
		$customer = $this->repo
							->whereHas('user', function ($user)
							{
								$user->where('email', Input::get('email'));
							})
							->get()
							->first();

		if ($customer->active === 0) {
			$response = array('message' => 'Akun anda belum diaktifkan oleh administrator.');
			return $this->response->errorBadRequest($response);
		}
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
		// little hack of heaven
		$str = strtotime(Input::get('dob') . ' + 1 day');
		$dob = date("Y/m/d", $str);

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
			$customer->dob        = $dob;
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
