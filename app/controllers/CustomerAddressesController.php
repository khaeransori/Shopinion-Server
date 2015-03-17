<?php

class CustomerAddressesController extends \BaseController {

	function __construct(CustomerAddress $repo, Customer $customer, REST $rest) {
		$this->customer = $customer;
		$this->repo = $repo;
		$this->rest = $rest;
	}

	/**
	 * Display a listing of customeraddresses
	 *
	 * @return Response
	 */
	public function index()
	{
		# TODO
		// $customeraddresses = Customeraddress::all();

		// return View::make('customeraddresses.index', compact('customeraddresses'));
	}

	/**
	 * Store a newly created customeraddress in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$this->customer->findOrFail(Input::get('customer_id'));

		$address = new $this->repo;

		if ($address->save()) {
			return $this->rest->response(201, $address);
		}

		return $this->response->errorBadRequest($address->errors());
	}

	/**
	 * Display the specified customeraddress.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$address = $this->repo->with('customer')->findOrFail($id);

		return $this->rest->response(200, $address);
	}

	/**
	 * Update the specified customeraddress in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$address = $this->repo->findOrFail($id);
		$this->customer->findOrFail($address->customer_id);

		if ($address->save()) {
			return $this->rest->response(201, $address);
		}

		return $this->response->errorBadRequest($address->errors());
	}

	/**
	 * Remove the specified customeraddress from storage.
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
