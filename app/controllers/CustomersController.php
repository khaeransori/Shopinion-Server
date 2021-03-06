<?php

class CustomersController extends \BaseController {

	function __construct(Customer $repo, User $user, REST $rest) {
		$this->user = $user;
		$this->repo = $repo;
		$this->rest = $rest;
	}
	/**
	 * Display a listing of customers
	 *
	 * @return Response
	 */
	public function index()
	{
		$search = Input::get('search', 0);
		$active = Input::get('active', 0);

		$customers = $this->repo;

		if ($search == 1) {
			$term = Input::get('term');
			$customers = $customers->where(function ($query) use ($term)
			{
				$query->where('first_name', 'like', "%$term%")
						->orWhere('last_name', 'like', "%$term%");
			});
		}

		if ($active == 1) {
			$customers = $customers->where('active', '=', 1);
		}
		
		$customers = $customers->with('user', 'addresses')->get();
		return $this->rest->response(200, $customers);
	}

	/**
	 * Store a newly created customer in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$before_save_rules = array(
			'first_name'	=> 'required|min:3',
			'last_name' 	=> 'required|min:3',
			'phone'			=> 'required'
		);

		$user 							= new $this->user;
        $user->email 					= Input::get('email');
        $user->password 				= Input::get('password');
        $user->password_confirmation 	= Input::get('password');
        $user->confirmation_code 		= md5(uniqid(mt_rand(), true));
        $user->confirmed 				= 1;

		$customer 				= new $this->repo;
		$customer->first_name	= Input::get('first_name');
		$customer->last_name	= Input::get('last_name');
		$customer->phone		= Input::get('phone');

		$validator = Validator::make(Input::all(), $before_save_rules);

		if ($validator->passes()) {
			if ($user->save()) {
					$customer->user_id 		= $user->id;
					$customer->save();

					return $this->rest->response(201, $customer);
			}
			
			return $this->response->errorBadRequest($user->errors());
		}

		return $this->response->errorBadRequest($validator->errors());
	}

	/**
	 * Display the specified customer.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$customer = $this->repo->with('user', 'addresses')->findOrFail($id);

		return $this->rest->response(200, $customer);
	}

	/**
	 * Update the specified customer in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$customer 	= $this->repo->findOrFail($id);
		$user 		= $this->user->findOrFail($customer->user_id);

		$customer->first_name	= Input::get('first_name');
		$customer->last_name	= Input::get('last_name');
		$customer->phone		= Input::get('phone');
		$customer->note			= Input::get('note');

		if ($customer->save()) {
			$user->email 					= Input::get('email');

			if (Input::has('password')) {
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

	/**
	 * Remove the specified customer from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$customer = $this->repo->with('user', 'addresses')->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $customer);
		}

		return $this->response->errorBadRequest();
	}

}
