<?php

use Dingo\Api\Routing\ControllerTrait;

class MobileCartsController extends Controller {

	use ControllerTrait;

	function __construct(CartProduct $repo, REST $rest, Cart $cart, Product $product, ProductAttribute $product_attribute) {
		$this->protect();

		$this->repo = $repo;
		$this->rest = $rest;
		$this->cart = $cart;
		$this->product = $product;
		$this->product_attribute = $product_attribute;
	}

	/**
	 * Display a listing of the resource.
	 * GET /mobilecarts
	 *
	 * @return Response
	 */
	public function index()
	{
		$user = API::user();
		$cart = $this->cart
					->where('customer_id', $user->customer->id)
					->where('is_customer', 1)
					->where('ordered', 0)
					->first();

		if (is_null($cart)) {
			return $this->response->errorNotFound();
		}

		$cart->load(
			'products.product.images',
			'products.combination.attribute_combinations.group'
		);

		return $this->rest->response(200, $cart);
	}

	/**
	 * Store a newly created resource in storage.
	 * POST /mobilecarts
	 *
	 * @return Response
	 */
	public function store()
	{
		$product_id 			= Input::get('product_id');
		$product_attribute_id 	= Input::get('product_attribute_id', 0);
		$qty 					= Input::get('qty', 0);

		$user = API::user();
		$user->load('customer');

		// ambil cart yang sedang aktif
		$cart = $this->cart
					->where('customer_id', $user->customer->id)
					->where('is_customer', 1)
					->where('ordered', 0)
					->first();

		$message = array(
				'qty' => array(
					'message' => ['Available stock is not enough to add to cart']
				)
			);

		$product = $this->product->with('productStock')->findOrFail($product_id);
		
		if (!($product_attribute_id === 0) && !($product_attribute_id === '0')) {
			$product_attribute = $this->product_attribute->with('stock')->findOrFail($product_attribute_id);

			if ($product_attribute->stock->qty < $qty) {

				return $this->response->errorBadRequest($message);

			}
		} else {
			if ($product->product_stock->qty < $qty) {

				return $this->response->errorBadRequest($message);

			}
		}

		$exists = $this->repo
						->whereCartId($cart->id)
						->whereProductId($product_id);
		
		if ($product_attribute_id !== 0) {
			$exists = $exists->whereProductAttributeId($product_attribute_id);
		}

		$exists = $exists->first();

		if (!(is_null($exists))) {
			$cart_product = $exists;
		} else {
			$cart_product = new $this->repo;
		}

		$cart_product->cart_id = $cart->id;
		if ($cart_product->save())
		{
			$response = array(
				'id' 	=> $cart->id,
				'qty'	=> $cart->products->count()
			);
			return $this->rest->response(201, $response, false);
		}

		return $this->response->errorBadRequest($cart_product->errors());
	}

	/**
	 * Display the specified resource.
	 * GET /mobilecarts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 * GET /mobilecarts/{id}/edit
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
	 * PUT /mobilecarts/{id}
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
	 * DELETE /mobilecarts/{id}
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$cartproduct = $this->repo
						->findOrFail($id);
						
		if ($this->repo->destroy($id)) {
			return $this->rest->response(202, $cartproduct);
		}

		return $this->response->errorBadRequest();
	}

}