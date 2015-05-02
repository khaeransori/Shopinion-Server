<?php

use Dingo\Api\Routing\ControllerTrait;

class MobileCustomerAddressesController extends Controller {

	use ControllerTrait;

	function __construct(CustomerAddress $repo, Customer $customer, REST $rest) {
		$this->protect();

		$this->customer = $customer;
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /mobilecustomeraddresses
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = API::user();
		$customer = $this->customer
					->where('active', 1)
					->where('user_id', $user->id)
					->first();

		if (is_null($customer)) {
			return $this->response->errorNotFound();
		}

		$address = new $this->repo;
		$address->customer_id = $customer->id;
		$address->fill(Input::all());


		if ($address->save()) {
			return $this->rest->response(201, $address);
		}

		return $this->response->errorBadRequest($address->errors());

	}

	/**
	 * Display the specified resource.
	 * GET /mobilecustomeraddresses/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$address = $this->repo->findOrFail($id);

		return $this->rest->response(200, $address);
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /mobilecustomeraddresses/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = API::user();
		$customer = $this->customer
					->where('active', 1)
					->where('user_id', $user->id)
					->first();

		if (is_null($customer)) {
			return $this->response->errorNotFound();
		}

		$address = $this->repo->findOrFail($id);
		$address->fill(Input::all());
		
		if ($address->save()) {
			return $this->rest->response(201, $address);
		}

		return $this->response->errorBadRequest($address->errors());
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /mobilecustomeraddresses/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$address = $this->repo->findOrFail($id);

		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $address);
		}

		return $this->response->errorBadRequest();
	}

}