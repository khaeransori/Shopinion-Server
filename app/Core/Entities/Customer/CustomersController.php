<?php namespace App\Core\Entities\Customer;

use App\Core\Criteria\ActiveCriteria;
use App\Core\Entities\Cart\CartRepository;
use App\Core\Entities\Customer\CustomerRepository;
use App\Core\Entities\User\UserRepository;
use Dingo\Api\Routing\ControllerTrait;
use Illuminate\Support\Facades\DB;
use Rhumsaa\Uuid\Uuid;
use Zizaco\Confide\UserValidator;

class CustomersController extends \Controller {

	use ControllerTrait;

	protected $cart;
	protected $repository;
	protected $user;

	function __construct(CartRepository $cart, CustomerRepository $repository, UserRepository $user) {
		$this->cart = $cart;
		$this->user = $user;
		$this->repository = $repository;
	}
	/**
	 * Display a listing of customers
	 *
	 * @return Response
	 */
	public function index()
	{ 
		try {
			$active = \Input::get('active', 0);
			$limit = \Input::get('limit', false);

			if (!($active === 0)) {
				$this->repository->pushCriteria(new ActiveCriteria());
			}
			
			$response = $this->repository->with(['user', 'addresses']);
			if (!($limit === false) && is_numeric($limit)) {
				$response = $response->paginate($limit);
			} else {
				$response = $response->all();
			}

			return $this->response->array($response->toArray());
		} catch (\Exception $e) {
			throw new Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
			
		}
	}

	/**
	 * Store a newly created customer in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		\DB::beginTransaction();

		try {
			$user = $this->user->getModel();
			$user->id 					 = Uuid::uuid4();
			$user->email                 = \Input::get('email');
			$user->password              = \Input::get('password');
			$user->password_confirmation = \Input::get('password');
			$user->confirmation_code     = md5(uniqid(mt_rand(), true));
			$user->is_customer			 = 1;
			if ($user->save()) {

					$customer 				= $this->repository->getModel();
					$customer->user_id		= (string) $user->id;
					$customer->first_name	= \Input::get('first_name');
					$customer->last_name	= \Input::get('last_name');
					$customer->phone		= \Input::get('phone');
					$customer->dob 			= \Carbon\Carbon::parse(\Input::get('dob'))->format('Y/m/d');

				if ($customer->save()) {
					\DB::commit();
					if (\Config::get('confide::signup_confirm')) {
		                \Mail::queueOn(
		                    \Config::get('confide::email_queue'),
		                    \Config::get('confide::email_account_confirmation'),
		                    compact('user'),
		                    function ($message) use ($user) {
		                        $message
		                            ->to($user->email, $user->username)
		                            ->subject(\Lang::get('confide::confide.email.account_confirmation.subject'));
		                    }
		                );
		            }

					return $this->response->array($customer->toArray());
				}

				\DB::rollBack();
				throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $customer->getErrors());
			}

			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $user->errors());

		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Display the specified customer.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		try {
			$repository = $this->repository->with(['user', 'addresses', 'carts.products', 'orders.detail'])->find($id);
			return $this->response->array($repository->toArray());
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Update the specified customer in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		try {
			$repository 	= $this->repository->getModel()->findOrFail($id);
			$user 			= $this->user->getModel()->findOrFail($repository->user_id);

			\DB::beginTransaction();
			$repository->fill(\Input::only(['first_name', 'last_name', 'dob', 'phone', 'active', 'note']));

			if ($repository->save()) {
				if (\Input::has('email')) {
					$user->email = \Input::get('email');
				}

				if (\Input::has('password')) {
					$user->password = \Input::get('password');
					$user->password_confirmation = \Input::get('password');
				}

				if ($user->save()) {
					\DB::commit();
					return $this->response->array($repository->toArray());
				}

				\DB::rollBack();
				throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $user->errors());
			}

			\DB::rollBack();
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $repository->getErrors());

		} catch (\LaravelBook\Ardent\InvalidModelException $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getErrors());
		} catch (\Exception $e) {
			\DB::rollBack();
			throw new \Dingo\Api\Exception\UpdateResourceFailedException("Error Processing Request", $e->getMessage());
		}
	}

	/**
	 * Remove the specified customer from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try {
			$repository = $this->repository->find($id);
			if ($this->repository->delete($id)) {
				return $this->response->array($repository->toArray());
			}
		} catch (\Exception $e) {
			throw new \Dingo\Api\Exception\ResourceException("Error Processing Request", $e->getMessage());
		}
	}

	/////////////////////////////
	public function forgotPassword()
	{
		if (\Confide::forgotPassword(\Input::get('email'))) {
            $notice_msg = \Lang::get('confide::confide.alerts.password_forgot');
            $response = array('message' => $notice_msg);
            return $this->response->array($response);
        }

        $error_msg = \Lang::get('confide::confide.alerts.wrong_password_forgot');
        $response = array('message' => $error_msg);
        return $this->response->array($response);
	}

	public function initCustomer()
	{
		$email = \Input::get('email', 0);
		$response = array();
		$customer = null;

		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$customer = $this->repository
	        						->getModel()
	        						->where('user_id', $user->id)
	        						->first();
	        }
	    } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
			$customer = $this->repository
								->getModel()
								->whereHas('user', function ($query) use ($email)
								{
									$query->where('email', $email);
								})
								->first();
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
	    } catch (\Exception $e) {
	    }

	    if (isset($customer) && !(is_null($customer)) && ($customer->active === 0)) {
			$customer = null;
		}

		if (isset($customer) && !(is_null($customer))) {
			$user 	= $this->user->find($customer->user_id);
			$token 	= \JWTAuth::fromUser($user);

			$response = array(
				'account' 			=> $customer,
				'token'   			=> $token,
				'wishlist'			=> $customer->wishlist()->count()
		    );

			// cek apakah sudah punya cart
			$cart = $this->cart
						->getModel()
						->where('customer_id', $customer->id)
						->where('is_customer', 1)
						->where('ordered', 0)
						->get()
						->first();

			if (is_null($cart)) {
				// kosong, bikin cart baru
				$new_cart = array(
					'customer_id' 			=> $customer->id,
					'carrier_id' 			=> 0,
					'delivery_address_id' 	=> 0,
					'invoice_address_id' 	=> 0,
					'is_customer' 			=> 1,
					'ordered' 				=> 0
				);

				$cart = $this->cart->create($new_cart);

				$response['cart'] = array(
					'id' => $cart->id,
					'qty'=> 0
				);
			} else {

				$response['cart'] = array(
					'id' => $cart->id,
					'qty'=> $cart->products()->count()
				);
			}

			$customer->last_visited_at = date('Y-m-d H:i:s');
			$customer->save();
			
			$response['account']['email'] = $user->email;
		}

		return $this->response->array($response);
	}

	public function loginCustomer()
	{
		// grab credentials from the request
	    $credentials = \Input::only('email', 'password');

	    try {
	        // attempt to verify the credentials and create a token for the user
	        if (! $token = \JWTAuth::attempt($credentials)) {
	        	throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException("Username atau Password anda salah");
	        }
	    } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
	        // something went wrong whilst attempting to encode the token
	        throw new Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException("Could not create token");
	    }

	    $user = \JWTAuth::authenticate($token);
		$customer = $this->repository->findByField('user_id', $user->id);

		if ($customer->active === 0) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException("Akun anda belum diaktifkan oleh administrator.");
		}

	    $response = array(
			'account' 			=> $customer,
			'token'   			=> $token,
			'wishlist'			=> $customer->wishlist()->count()
	    );

	    // cek apakah sudah punya cart
		$cart = $this->cart
					->getModel()
					->where('customer_id', $customer->id)
					->where('is_customer', 1)
					->where('ordered', 0)
					->get()
					->first();

		if (is_null($cart)) {
			// kosong, bikin cart baru
			$new_cart = array(
				'customer_id' 			=> $customer->id,
				'carrier_id' 			=> 0,
				'delivery_address_id' 	=> 0,
				'invoice_address_id' 	=> 0,
				'is_customer' 			=> 1,
				'ordered' 				=> 0
			);

			$cart = $this->cart->create($new_cart);

			$response['cart'] = array(
				'id' => $cart->id,
				'qty'=> 0
			);
		} else {

			$response['cart'] = array(
				'id' => $cart->id,
				'qty'=> $cart->products()->count()
			);
		}

		$customer->last_visited_at = date('Y-m-d H:i:s');
		$customer->save();

		$response['account']['email'] = $user->email;
	    return $this->response->array($response);
	}

	public function detailCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
	        	$repository = $this->repository->with(['user', 'addresses', 'carts.products', 'orders.detail'])->findByField('user_id', $user->id);
				return $this->response->array($repository->toArray());
	        }
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    	
	    }
	}

	public function updateCustomer()
	{
		try {
	        if ($user = \JWTAuth::parseToken()->authenticate()) {
				$user 			= $this->user->getModel()->findOrFail($user->id);
	        	$repository 	= $this->repository->findByField('user_id', $user->id);

				\DB::beginTransaction();

				// try {
				$repository->fill(\Input::only(['first_name', 'last_name', 'dob', 'phone']));

				if ($repository->save()) {
					if (\Input::has('email')) {
						$user->email = \Input::get('email');
					}

					if (\Input::has('password')) {
						$user->password = \Input::get('password');
						$user->password_confirmation = \Input::get('password');
					}

					if ($user->save()) {
						\DB::commit();
						$repository['email'] = $user->email;
						return $this->response->array($repository->toArray());
					}

					\DB::rollBack();
					throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $user->errors());
				}
	        }
	    } catch (\Dingo\Api\Exception\StoreResourceFailedException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\LaravelBook\Ardent\InvalidModelException $e) {
			throw new \Dingo\Api\Exception\StoreResourceFailedException("Error Processing Request", $e->getErrors());
	    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
			throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    } catch (\Exception $e) {
	    	throw new \Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException();
	    }
	}
}
