<?php

use Dingo\Api\Routing\ControllerTrait;

class MobileCustomersController extends Controller {

	use ControllerTrait;

	function __construct(Cart $cart, Category $category, Customer $repo, User $user, REST $rest) {
		$this->protect('detail', 'update');

		$this->cart 	= $cart;
		$this->category = $category;
		$this->user     = $user;
		$this->repo     = $repo;
		$this->rest     = $rest;
	}

	public function forgotPassword()
	{
		if (Confide::forgotPassword(Input::get('email'))) {
            $notice_msg = Lang::get('confide::confide.alerts.password_forgot');
            $response = array('message' => $notice_msg);
            return $this->rest->response(200, $response, false);
        }

        $error_msg = Lang::get('confide::confide.alerts.wrong_password_forgot');
        $response = array('message' => $error_msg);
        return $this->rest->response(200, $response, false);
	}
	
	public function init()
	{
		$root = $this->category->root();
		$root->load([
			'children' => function ($children)
			{
				$children->whereActive(1)
							->orderBy('name', 'ASC');
			}
			]);

		$response['category'] = $root;

		try {
	        if (JWTAuth::parseToken()->authenticate()) {
	        	$customer = $this->repo
					->where('active', 1)
					->whereHas('user', function ($query)
					{
						$query->where('email', Input::get('email'));
					})
					->first();
	        }
	    } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$customer = $this->repo
				->where('active', 1)
				->whereHas('user', function ($query)
				{
					$query->where('email', Input::get('email'));
				})
				->first();
	    } catch (Exception $e) {
	    }


		if (isset($customer) && !(is_null($customer))) {
			$token = JWTAuth::fromUser($customer->user);
			$customer->load('user');
			$wishlist_count = $customer->wishlist->count();

			$response['account'] = $customer;
			$response['token']   = $token;
			$response['wishlist_count'] = $wishlist_count;

			// cek apakah sudah punya cart
			$cart = $this->cart
						->where('customer_id', $customer->id)
						->where('is_customer', 1)
						->where('ordered', 0)
						->first();

			if (is_null($cart)) {
				// kosong, bikin cart baru
				$cart = new $this->cart();

				$cart->customer_id 			= $customer->id;
				$cart->carrier_id 			= 0;
				$cart->delivery_address_id 	= 0;
				$cart->invoice_address_id 	= 0;
				$cart->is_customer 			= 1;
				$cart->ordered 				= 0;
				
				$cart->save();

				$response['cart'] = array(
					'id' => $cart->id,
					'qty'=> 0
				);
			} else {
				$cart_products_count = $cart->products->count();

				$response['cart'] = array(
					'id' => $cart->id,
					'qty'=> $cart_products_count
				);
			}

			$customer->last_visited_at = date('Y-m-d H:i:s');
			$customer->save();
			
		}

		return $this->rest->response(200, $response, false);
	}

	public function login()
	{
		// grab credentials from the request
	    $credentials = Input::only('email', 'password');

	    try {
	        // attempt to verify the credentials and create a token for the user
	        if (! $token = JWTAuth::attempt($credentials)) {
	        	$response = array('message' => 'Username atau Password anda salah');
	        	return $this->response->errorBadRequest($response);
	        }
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        // something went wrong whilst attempting to encode the token
	        $response = array('message' => 'Could not create token');
	        return $this->response->error($response, 500);
	    }

		$customer = $this->repo
							->whereHas('user', function ($user)
							{
								$user->where('email', Input::get('email'));
							})
							->first();

		if ($customer->active === 0) {
			$response = array('message' => 'Akun anda belum diaktifkan oleh administrator.');
			return $this->response->errorBadRequest($response);
		}
		$customer->load('user');
		$wishlist_count = $customer->wishlist->count();


	    $response = array(
			'account' 			=> $customer,
			'token'   			=> $token,
			'wishlist_count'	=> $wishlist_count
	    	);

	    // cek apakah sudah punya cart
		$cart = $this->cart
					->where('customer_id', $customer->id)
					->where('is_customer', 1)
					->where('ordered', 0)
					->first();

		if (is_null($cart)) {
			// kosong, bikin cart baru
			$cart = new $this->cart();

			$cart->customer_id 			= $customer->id;
			$cart->carrier_id 			= 0;
			$cart->delivery_address_id 	= 0;
			$cart->invoice_address_id 	= 0;
			$cart->is_customer 			= 1;
			$cart->ordered 				= 0;
			
			$cart->save();

			$response['cart'] = array(
				'id' => $cart->id,
				'qty'=> 0
			);
		} else {
			$cart_products_count = $cart->products->count();

			$response['cart'] = array(
				'id' => $cart->id,
				'qty'=> $cart_products_count
			);
		}
	    return $this->rest->response(200, $response, false);
	}

	public function register()
	{
		// little hack of heaven
		$str = strtotime(Input::get('dob'));
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

	public function detail()
	{
		$user = API::user();
		$customer = $this->repo
					->with('addresses', 'user')
					->where('active', 1)
					->whereHas('user', function ($query) use ($user)
					{
						$query->where('email', $user->email);
					})
					->first();

		if (is_null($customer)) {
			return $this->response->errorNotFound();
		}

		return $this->rest->response(200, $customer);
	}

	public function update()
	{
		$password = Input::get('password', false);
		// little hack of heaven
		$str = strtotime(Input::get('dob') . ' + 1 day');
		$dob = date("Y/m/d", $str);

		$user = API::user();
		$customer = $this->repo
					->with('addresses', 'user')
					->where('active', 1)
					->whereHas('user', function ($query) use ($user)
					{
						$query->where('email', $user->email);
					})
					->first();

		$customer->first_name = Input::get('first_name');
		$customer->last_name = Input::get('last_name');
		$customer->dob = $dob;
		$customer->phone = Input::get('phone');

		if ($customer->save()) {
			if (!($password === false)) {
		        $user->password 				= Input::get('password');
		        $user->password_confirmation 	= Input::get('password');
			}
			
	        if ($user->save()) {
				return $this->rest->response(202, $customer);
	        }

			return $this->response->errorBadRequest($user->errors());

		}

		return $this->response->errorBadRequest($customer->errors());
	}

}
