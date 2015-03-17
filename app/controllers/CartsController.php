<?php

class CartsController extends \BaseController {

	function __construct(Cart $repo, REST $rest, Customer $customer, CustomerAddress $address, Carrier $carrier) {
		$this->repo 	= $repo;
		$this->rest 	= $rest;
		$this->customer = $customer;
		$this->address 	= $address;
		$this->carrier  = $carrier;
	}
	/**
	 * Display a listing of the resource.
	 * GET /carts
	 *
	 * @return Response
	 */
	public function index()
	{
		$customer_id = Input::get('customer_id', FALSE);
		$ordered = Input::get('ordered', FALSE);

		$carriers = $this->repo
							->with(
								'carrier',
								'customer',
								'delivery_address',
								'invoice_address',
								'order'
								);

		if (FALSE !== $customer_id) {
			$this->customer->findOrFail($customer_id);
			$carriers = $carriers->where('customer_id', $customer_id);
		}

		if (FALSE !== $ordered) {
			$carriers = $carriers->where('ordered', $ordered);
		}
		
		$carriers = $carriers->get();

		return $this->rest->response(200, $carriers);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /carts
	 *
	 * @return Response
	 */
	public function store()
	{
		$carrier_id 		 = Input::get('carrier_id', 0);
		$delivery_address_id = Input::get('delivery_address_id', 0);
		$invoice_address_id	 = Input::get('invoice_address_id', 0);

		$this->customer->findOrFail(Input::get('customer_id'));

		if ($carrier_id !== 0) {
			$carrier = $this->carrier->findOrFail($carrier_id);

			if ($carrier->on_store !== 1) {
				if ($delivery_address_id !== 0) {
					$this->address->findOrFail($delivery_address_id);
				} else {
					$errors = array(
						'message' => array(
							'delivery_address_id' => ['The delivery address field is required.']
						)
					);

					return $this->response->errorBadRequest($errors);
				}

				if ($invoice_address_id !== 0) {
					$this->address->findOrFail($invoice_address_id);
				} else {
					$errors = array(
						'message' => array(
							'invoice_address_id' => ['The invoice address field is required.']
						)
					);

					return $this->response->errorBadRequest($errors);
				}
			}
		}
		

		$cart = new $this->repo;

		if ($cart->save())
		{
			return $this->rest->response(201, $cart);
		}

		return $this->response->errorBadRequest($cart->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$cart = $this->repo->findOrFail($id);
		$cart->load(
			'carrier',
			'customer.user',
			'delivery_address',
			'invoice_address',
			'products.product.images',
			'products.combination.attribute_combinations.group',
			'order');
		
		return $this->rest->response(200, $cart);
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /carts/{id}/edit
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 * PUT /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 * DELETE /carts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$product = $this->repo->findOrFail($id);
		if ($product->ordered === 0) {
			if ($this->repo->destroy($id)) {
				return $this->rest->response(202, $product);
			}
		}

		return $this->response->errorBadRequest();
	}

}